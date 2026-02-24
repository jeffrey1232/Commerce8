<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;
use App\Models\Vendor;
use App\Models\PointRelais;
use App\Services\ColisService;
use App\Services\PaiementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method bool hasRole(string $role)
 */
class ColisController extends Controller
{
    protected $colisService;
    protected $paiementService;

    public function __construct(
        ColisService $colisService,
        PaiementService $paiementService
    ) {
        $this->colisService = $colisService;
        $this->paiementService = $paiementService;
    }

    /**
     * Vérifie si l'utilisateur a le rôle spécifié
     */
    private function userHasRole(string $role): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Vérification directe en base de données pour éviter les erreurs de linting
        return \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', $role)
            ->exists();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Colis::with(['vendor', 'pointRelais', 'client']);

        // Filtrer selon le rôle de l'utilisateur
        if ($this->userHasRole('vendor') && $user->vendor) {
            $query->where('vendor_id', $user->vendor->id);
        } elseif ($this->userHasRole('staff') && $user->managedPointRelais) {
            $query->where('point_relais_id', $user->managedPointRelais->id);
        }

        // Filtrer par statut
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrer par période
        if ($request->has('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        $colis = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('colis.index', compact('colis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$this->userHasRole('vendor') || !$user->vendor) {
            abort(403, 'Unauthorized');
        }

        $pointsRelais = PointRelais::where('status', 'active')->get();

        return view('colis.create', compact('pointsRelais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->userHasRole('vendor') || !$user->vendor) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'point_relais_id' => 'required|exists:points_relais,id',
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'product_photo' => 'nullable|image|max:2048',
            'fitting_option' => 'boolean',
            'client_phone' => 'nullable|string',
            'client_email' => 'nullable|email',
        ]);

        try {
            $validated['vendor_id'] = $user->vendor->id;

            $colis = $this->colisService->createColis($validated);

            return response()->json([
                'success' => true,
                'message' => 'Colis créé avec succès',
                'colis' => $colis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $colis = Colis::with(['vendor', 'pointRelais', 'client', 'statusLogs', 'paiement', 'reversement'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('colis.show', compact('colis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $colis = Colis::findOrFail($id);

        // Vérifier les permissions
        $this->authorizeColisAccess($colis);

        $pointsRelais = PointRelais::where('status', 'active')->get();

        return view('colis.edit', compact('colis', 'pointsRelais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $colis = Colis::where('uuid', $uuid)->firstOrFail();

        // Vérifier les permissions
        $this->authorizeColisAccess($colis);

        $validated = $request->validate([
            'product_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'fitting_option' => 'boolean',
            'client_phone' => 'nullable|string',
            'client_email' => 'nullable|email',
        ]);

        try {
            $colis->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Colis mis à jour avec succès',
                'colis' => $colis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $colis = Colis::where('uuid', $uuid)->firstOrFail();

        // Vérifier les permissions
        $this->authorizeColisAccess($colis);

        // Seul le vendeur peut supprimer ses colis non déposés
        if ($colis->status !== 'created') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les colis non déposés peuvent être supprimés'
            ], 400);
        }

        try {
            $colis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Colis supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Déposer un colis au point relais
     */
    public function deposit($uuid)
    {
        $colis = Colis::where('uuid', $uuid)->firstOrFail();

        // Seul le staff peut déposer les colis
        if (!$this->userHasRole('staff')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $updatedColis = $this->colisService->depositColis($colis->id, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Colis déposé avec succès',
                'colis' => $updatedColis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Retirer un colis
     */
    public function withdraw(Request $request, $uuid)
    {
        $colis = Colis::where('uuid', $uuid)->firstOrFail();

        // Seul le staff peut retirer les colis
        if (!$this->userHasRole('staff')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
        ]);

        try {
            $updatedColis = $this->colisService->withdrawColis($colis->id, $validated['client_id'], Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Colis retiré avec succès',
                'colis' => $updatedColis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mettre à jour le statut d'un colis
     */
    public function updateStatus(Request $request, $uuid)
    {
        $colis = Colis::where('uuid', $uuid)->firstOrFail();

        // Seul le staff peut changer le statut
        if (!$this->userHasRole('staff')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:deposited,pending_withdrawal,in_fitting,paid,refused,returned',
            'reason' => 'nullable|string',
        ]);

        try {
            $updatedColis = $this->colisService->updateStatus($colis->id, $validated['status'], $validated['reason'], Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'colis' => $updatedColis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Page de paiement pour un colis
     */
    public function payment($uuid)
    {
        $colis = Colis::where('uuid', $uuid)->firstOrFail();

        if (!$colis->canBePaid()) {
            abort(400, 'Ce colis ne peut pas être payé');
        }

        return view('colis.payment', compact('colis'));
    }

    /**
     * Traiter le paiement d'un colis
     */
    public function processPayment(Request $request, $uuid)
    {
        $colis = Colis::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'provider' => 'required|in:wave,orange_money,mtn,cash',
            'payment_method' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'idempotency_key' => 'nullable|string',
        ]);

        try {
            $paiement = $this->paiementService->initiatePayment($colis->id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Paiement initié avec succès',
                'paiement' => $paiement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Méthodes privées

    private function authorizeColisAccess(Colis $colis)
    {
        $user = Auth::user();

        // Admin peut accéder à tous les colis
        if ($this->userHasRole('admin')) {
            return true;
        }

        // Le vendeur peut accéder à ses colis
        if ($this->userHasRole('vendor') && $user->vendor && $colis->vendor_id === $user->vendor->id) {
            return true;
        }

        // Le staff peut accéder aux colis de son point relais
        if ($this->userHasRole('staff') && $user->managedPointRelais && $colis->point_relais_id === $user->managedPointRelais->id) {
            return true;
        }

        abort(403, 'Unauthorized');
    }
}
