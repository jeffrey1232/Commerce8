<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;
use App\Models\Vendor;
use App\Models\Paiement;
use App\Models\Reversement;
use App\Models\PointRelais;
use App\Models\Cabine;
use App\Models\Essai;
use App\Services\ColisService;
use App\Services\PaiementService;
use App\Services\ReversementService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $colisService;
    protected $paiementService;
    protected $reversementService;

    public function __construct(
        ColisService $colisService,
        PaiementService $paiementService,
        ReversementService $reversementService
    ) {
        $this->colisService = $colisService;
        $this->paiementService = $paiementService;
        $this->reversementService = $reversementService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'today');

        // Statistiques générales
        $stats = [
            'total_colis' => Colis::count(),
            'colis_today' => Colis::whereDate('created_at', today())->count(),
            'colis_deposited' => Colis::where('status', 'deposited')->count(),
            'colis_paid' => Colis::where('status', 'paid')->count(),
            'total_vendors' => Vendor::where('status', 'approved')->count(),
            'total_points_relais' => PointRelais::where('status', 'active')->count(),
        ];

        // Statistiques de livraison
        $deliveryStats = $this->getDeliveryStats($period);

        // Colis en retard
        $overdueColis = Colis::where('storage_deadline', '<', now())
            ->whereIn('status', ['deposited', 'pending_withdrawal'])
            ->with(['vendor', 'pointRelais'])
            ->take(5)
            ->get();

        // Statistiques vendeur si l'utilisateur est un vendeur
        $vendorStats = null;
        if ($user->hasRole('vendor') && $user->vendor) {
            $vendorStats = $this->colisService->getVendorStats($user->vendor->id, $period);
        }

        // Statistiques des paiements
        $paymentStats = $this->paiementService->getPaymentStats($period);

        // Reversements récents
        $recentReversements = Reversement::with('vendor')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Statistiques des cabines
        $cabinStats = [
            'total' => Cabine::count(),
            'available' => Cabine::where('status', 'available')->count(),
            'occupied' => Cabine::where('status', 'occupied')->count(),
            'trials_today' => Essai::whereDate('created_at', today())->count(),
        ];

        // Cabines disponibles
        $availableCabines = Cabine::with('pointRelais')
            ->where('status', 'available')
            ->take(3)
            ->get();

        // Essais récents
        $recentTrials = Essai::with(['client', 'colis', 'cabine'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Données pour le graphique
        $chartData = $this->getChartData($period);

        return view('dashboard', compact(
            'stats',
            'deliveryStats',
            'overdueColis',
            'vendorStats',
            'paymentStats',
            'recentReversements',
            'cabinStats',
            'availableCabines',
            'recentTrials',
            'chartData'
        ));
    }

    private function getDeliveryStats(string $period): array
    {
        $query = Colis::query();

        switch ($period) {
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

        $colis = $query->get();

        return [
            'total' => $colis->count(),
            'deposited' => $colis->where('status', 'deposited')->count(),
            'withdrawn' => $colis->whereIn('status', ['pending_withdrawal', 'in_fitting'])->count(),
            'paid' => $colis->where('status', 'paid')->count(),
            'returned' => $colis->where('status', 'returned')->count(),
            'completion_rate' => $colis->count() > 0 ?
                ($colis->where('status', 'paid')->count() / $colis->count()) * 100 : 0,
        ];
    }

    private function getChartData(string $period): array
    {
        $labels = [];
        $data = [];

        if ($period === 'today') {
            // Données horaires pour aujourd'hui
            for ($i = 0; $i < 24; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $data[] = Colis::whereDate('created_at', today())
                    ->whereHour('created_at', $i)
                    ->count();
            }
        } elseif ($period === 'week') {
            // Données quotidiennes pour la semaine
            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            foreach ($days as $i => $day) {
                $labels[] = $day;
                $data[] = Colis::whereBetween('created_at', [
                    now()->startOfWeek()->addDays($i)->startOfDay(),
                    now()->startOfWeek()->addDays($i)->endOfDay()
                ])->count();
            }
        } else {
            // Données mensuelles
            for ($i = 1; $i <= 30; $i++) {
                $labels[] = "Day $i";
                $data[] = Colis::whereDay('created_at', $i)
                    ->whereMonth('created_at', now()->month)
                    ->count();
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getOverdueColis()
    {
        $overdueColis = Colis::where('storage_deadline', '<', now())
            ->whereIn('status', ['deposited', 'pending_withdrawal'])
            ->with(['vendor', 'pointRelais'])
            ->get();

        return response()->json($overdueColis);
    }

    public function getVendorStats(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('vendor') || !$user->vendor) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $period = $request->get('period', 'today');
        $stats = $this->colisService->getVendorStats($user->vendor->id, $period);

        return response()->json($stats);
    }

    public function getPaymentStats(Request $request)
    {
        $period = $request->get('period', 'today');
        $stats = $this->paiementService->getPaymentStats($period);

        return response()->json($stats);
    }

    public function getCabinStats()
    {
        $cabinStats = [
            'total' => Cabine::count(),
            'available' => Cabine::where('status', 'available')->count(),
            'occupied' => Cabine::where('status', 'occupied')->count(),
            'maintenance' => Cabine::where('status', 'maintenance')->count(),
            'trials_today' => Essai::whereDate('created_at', today())->count(),
        ];

        return response()->json($cabinStats);
    }
}
