<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;

class MultiRoleController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-multi-role');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Mettre à jour le dernier login
            $user->last_login = now();
            $user->save();

            // Rediriger selon le rôle
            return $this->redirectToDashboard($user);
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm()
    {
        return view('auth.register-multi-role');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|in:vendor,client',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'status' => 'active',
        ]);

        Auth::login($user);

        // Si c'est un vendeur, créer son profil vendeur
        if ($user->isVendor()) {
            $this->createVendorProfile($user);
        }

        return $this->redirectToDashboard($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login-multi-role');
    }

    public function showVendorSetupForm()
    {
        $user = Auth::user();
        
        if (!$user->isVendor()) {
            return redirect()->route('login.multi');
        }

        if ($user->vendor) {
            return redirect()->route('vendor.dashboard');
        }

        return view('auth.vendor-setup');
    }

    public function setupVendor(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isVendor()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
            'business_license' => 'nullable|string|max:255',
            'business_description' => 'nullable|string',
        ]);

        $vendor = $this->createVendorProfile($user, $validated);

        return response()->json([
            'success' => true,
            'vendor' => $vendor,
            'message' => 'Profil vendeur créé avec succès'
        ]);
    }

    private function createVendorProfile(User $user, array $additionalData = []): Vendor
    {
        // Si le vendeur existe déjà, le retourner
        if ($user->vendor) {
            return $user->vendor;
        }

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'store_name' => $additionalData['store_name'] ?? $user->name . ' Store',
            'store_address' => $additionalData['store_address'] ?? $user->address,
            'store_phone' => $additionalData['store_phone'] ?? $user->phone,
            'business_license' => $additionalData['business_license'] ?? null,
            'commission_rate' => 5.00, // Taux par défaut
            'rating' => 0.00,
            'total_packages' => 0,
            'total_revenue' => 0.00,
            'status' => 'active',
        ]);

        // Créer le wallet automatiquement
        Wallet::create([
            'vendor_id' => $vendor->id,
            'balance' => 0,
            'pending_balance' => 0,
            'total_earned' => 0,
            'total_withdrawn' => 0,
        ]);

        return $vendor;
    }

    private function redirectToDashboard(User $user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'vendor':
                if (!$user->vendor) {
                    return redirect()->route('vendor.setup');
                }
                return redirect()->route('vendor.dashboard');
            case 'client':
                return redirect()->route('client.validation');
            case 'community_manager':
                return redirect()->route('studio.dashboard');
            case 'staff':
                return redirect()->route('admin.dashboard'); // Staff a accès à l'admin
            default:
                return redirect('/login-multi-role');
        }
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $exists = User::where('email', $validated['email'])->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Cet email est déjà utilisé' : 'Email disponible'
        ]);
    }

    public function getUserProfile(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'address' => $user->address,
            'status' => $user->status,
            'last_login' => $user->last_login,
            'created_at' => $user->created_at,
        ];

        // Ajouter les informations spécifiques au rôle
        if ($user->isVendor() && $user->vendor) {
            $profile['vendor'] = $user->vendor->load('wallet');
        }

        return response()->json($profile);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|string|confirmed|min:8',
        ]);

        // Vérifier le mot de passe actuel si changement de mot de passe
        if (isset($validated['password']) && !Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'error' => 'Le mot de passe actuel est incorrect',
                'message' => 'Veuillez vérifier votre mot de passe actuel'
            ], 422);
        }

        $user->update(array_intersect_key($validated, array_flip(['name', 'phone', 'address'])));

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->save();
        }

        return response()->json([
            'success' => true,
            'user' => $user->fresh(),
            'message' => 'Profil mis à jour avec succès'
        ]);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $validated = $request->validate([
            'password' => 'required|string',
            'confirmation' => 'required|string|in:DELETE',
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'error' => 'Mot de passe incorrect',
                'message' => 'Veuillez vérifier votre mot de passe'
            ], 422);
        }

        // Soft delete
        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Compte supprimé avec succès'
        ]);
    }
}
