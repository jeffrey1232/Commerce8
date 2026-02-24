<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;
use App\Models\Vendor;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'êtes pas associé à un compte vendeur');
        }

        // Statistiques du vendeur
        $stats = [
            'pending_colis' => $vendor->colis()->where('status', 'created')->count(),
            'deposited_colis' => $vendor->colis()->where('status', 'deposited')->count(),
            'sold_colis' => $vendor->colis()->where('status', 'paid')->count(),
            'returned_colis' => $vendor->colis()->where('status', 'returned')->count(),
        ];

        // Solde du portefeuille
        $completedPayments = $vendor->colis()
            ->where('status', 'paid')
            ->with('paiement')
            ->get()
            ->sum(function($colis) {
                return $colis->paiement ? $colis->paiement->amount : 0;
            });

        $pendingPayments = $vendor->colis()
            ->where('status', 'deposited')
            ->sum('total_amount');

        $walletBalance = [
            'available' => $completedPayments * 0.95, // 95% après commission
            'pending' => $pendingPayments,
            'total_sales' => $completedPayments,
        ];

        // Colis récents
        $recentPackages = $vendor->colis()
            ->with('paiement')
            ->orderBy('created_at', 'desc')
            ->take(9)
            ->get()
            ->map(function($colis) {
                return [
                    'uuid' => $colis->uuid,
                    'tracking_code' => $colis->tracking_code,
                    'product_name' => $colis->product_name,
                    'total_amount' => $colis->total_amount,
                    'status' => $colis->status,
                    'created_at' => $colis->created_at->format('d/m H:i'),
                    'image' => $colis->image_url ?? 'https://picsum.photos/seed/product1/200/200.jpg'
                ];
            });

        // Données pour le graphique des ventes
        $salesData = $vendor->colis()
            ->where('status', 'paid')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as sales')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(7)
            ->get();

        return view('vendor.portal', compact(
            'stats',
            'walletBalance',
            'recentPackages',
            'salesData'
        ));
    }

    public function create()
    {
        $pointsRelais = \App\Models\PointRelais::where('status', 'active')->get();
        return view('vendor.create', compact('pointsRelais'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendeur non trouvé');
        }

        try {
            // Disable foreign key constraints for SQLite
            DB::statement('PRAGMA foreign_keys = OFF');

            $validated = $request->validate([
                'product_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'client_phone' => 'required|string',
                'client_email' => 'nullable|email',
                'fitting_fee' => 'nullable', // Checkbox, nullable car pas envoyé si non coché
                'product_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);

            // Handle file upload
            $productPhoto = null;
            if ($request->hasFile('product_photo')) {
                $file = $request->file('product_photo');
                $productPhoto = $file->store('products', 'public');
            }

            // Check if fitting option is selected
            $hasFittingFee = isset($validated['fitting_fee']) && $validated['fitting_fee'] === '500';

            // Créer le colis
            $colis = Colis::create([
                'uuid' => str()->uuid(),
                'tracking_code' => 'ECM' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'vendor_id' => $vendor->id,
                'product_name' => $validated['product_name'],
                'description' => $validated['description'] ?? '',
                'price' => $validated['price'],
                'shipping_fee' => 1000, // Frais de livraison fixes
                'fitting_fee' => $hasFittingFee ? 500 : 0,
                'total_amount' => $validated['price'] + 1000 + ($hasFittingFee ? 500 : 0),
                'client_phone' => $validated['client_phone'],
                'client_email' => $validated['client_email'] ?? '',
                'point_relais_id' => 1, // Point relais par défaut (ID 1 dans table points_relais)
                'status' => 'created',
                'product_photo' => $productPhoto,
            ]);

            // Re-enable foreign key constraints
            DB::statement('PRAGMA foreign_keys = ON');

            return redirect()->route('vendor.portal')->with('success', 'Colis créé avec succès! Code: ' . $colis->tracking_code);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-enable foreign key constraints in case of error
            DB::statement('PRAGMA foreign_keys = ON');
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Erreur de validation. Veuillez vérifier tous les champs.');
        } catch (\Exception $e) {
            // Re-enable foreign key constraints in case of error
            DB::statement('PRAGMA foreign_keys = ON');
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    public function show($uuid)
    {
        $user = Auth::user();
        $colis = Colis::where('uuid', $uuid)
            ->where('vendor_id', $user->vendor->id)
            ->with(['paiement', 'pointRelais'])
            ->firstOrFail();

        return view('vendor.show', compact('colis'));
    }
}
