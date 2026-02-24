<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;
use App\Models\Vendor;
use App\Models\Paiement;
use App\Models\Reversement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Statistiques réelles de la base de données
        $stats = [
            'total_colis' => Colis::count(),
            'colis_today' => Colis::whereDate('created_at', today())->count(),
            'colis_deposited' => Colis::where('status', 'deposited')->count(),
            'colis_paid' => Colis::where('status', 'paid')->count(),
            'total_vendors' => Vendor::where('status', 'approved')->count(),
            'pending_payments' => Paiement::where('status', 'pending')->count(),
        ];

        // Reversements en attente
        $pendingDisbursements = Vendor::with(['colis' => function($query) {
            $query->where('status', 'paid');
        }])
        ->whereHas('colis')
        ->get()
        ->map(function($vendor) {
            $paidColis = $vendor->colis->where('status', 'paid');
            $totalSales = $paidColis->sum('total_amount');
            $commission = $totalSales * 0.05; // 5% de commission
            $netPayout = $totalSales - $commission;

            return [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->business_name,
                'total_sales' => $totalSales,
                'commission' => $commission,
                'net_payout' => $netPayout,
                'paid_colis_count' => $paidColis->count()
            ];
        });

        // Activités récentes
        $recentActivities = collect([
            [
                'type' => 'deposit',
                'message' => 'Nouveau colis déposé',
                'details' => 'ECM12345678 - Fashion Store',
                'time' => 'Il y a 2 min',
                'status' => 'online'
            ],
            [
                'type' => 'payment',
                'message' => 'Paiement en cours',
                'details' => 'ECM87654321 - Client: Marie S.',
                'time' => 'Il y a 5 min',
                'status' => 'pending'
            ],
            [
                'type' => 'fitting',
                'message' => 'Essai terminé',
                'details' => 'Cabine 2 - Client satisfait',
                'time' => 'Il y a 10 min',
                'status' => 'online'
            ]
        ]);

        return view('admin.dashboard', compact(
            'stats',
            'pendingDisbursements',
            'recentActivities'
        ));
    }

    public function approveDisbursement($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        // Logique de validation du reversement
        return response()->json(['success' => true, 'message' => 'Reversement validé']);
    }

    public function getStats()
    {
        $stats = [
            'total_colis' => Colis::count(),
            'total_vendors' => Vendor::count(),
            'total_revenue' => Paiement::where('status', 'completed')->sum('amount'),
            'pending_payments' => Paiement::where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }
}
