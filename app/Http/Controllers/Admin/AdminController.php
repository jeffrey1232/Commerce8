<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Vendor;
use App\Models\Payment;
use App\Models\Wallet;
use App\Services\ReportService;
use App\Services\PaymentService;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_colis' => Package::count(),
            'total_vendors' => Vendor::where('status', 'active')->count(),
            'total_revenue' => Payment::where('payment_status', 'completed')->sum('net_amount'),
            'pending_payments' => Payment::where('payment_status', 'pending')->count(),
            'overdue_packages' => Package::where('status', 'overdue')->count(),
            'today_packages' => Package::whereDate('created_at', today())->count(),
            'active_wallets' => Wallet::where('balance', '>', 0)->count(),
        ];

        $pendingDisbursements = Payment::with('vendor')
            ->where('payment_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $overduePackages = Package::with('vendor')
            ->where('status', 'overdue')
            ->where('created_at', '<', now()->subDays(2))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $recentActivities = $this->getRecentActivities();

        return view('admin.dashboard', compact(
            'stats', 
            'pendingDisbursements', 
            'overduePackages',
            'recentActivities'
        ));
    }

    public function approveDisbursement($paymentId): JsonResponse
    {
        $payment = Payment::findOrFail($paymentId);
        $paymentService = new PaymentService();
        $result = $paymentService->processDisbursement($payment);

        if ($result['success']) {
            return response()->json([
                'message' => 'Reversement approuvé avec succès',
                'payment' => $payment->fresh()
            ]);
        }

        return response()->json([
            'error' => $result['message']
        ], 400);
    }

    public function generateReport(Request $request): JsonResponse
    {
        $reportService = new ReportService();
        $report = $reportService->generateMonthlyReport($request->all());
        
        return response()->json($report);
    }

    public function getVendors(Request $request): JsonResponse
    {
        $vendors = Vendor::with('user', 'wallet')
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('store_name', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
            })
            ->paginate(10);

        return response()->json($vendors);
    }

    public function updateVendorStatus(Request $request, $vendorId): JsonResponse
    {
        $vendor = Vendor::findOrFail($vendorId);
        $vendor->status = $request->status;
        $vendor->save();

        return response()->json([
            'message' => 'Statut du vendeur mis à jour',
            'vendor' => $vendor
        ]);
    }

    public function getPackages(Request $request): JsonResponse
    {
        $packages = Package::with('vendor.user')
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->vendor_id, function ($query, $vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('tracking_code', 'like', "%{$search}%")
                      ->orWhere('client_name', 'like', "%{$search}%")
                      ->orWhere('product_name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($packages);
    }

    public function updatePackageStatus(Request $request, $packageId): JsonResponse
    {
        $package = Package::findOrFail($packageId);
        $packageService = new PackageService();
        
        $result = $packageService->updateStatus($package, $request->status);

        if ($result) {
            return response()->json([
                'message' => 'Statut du colis mis à jour',
                'package' => $package->fresh()
            ]);
        }

        return response()->json([
            'error' => 'Impossible de mettre à jour le statut du colis'
        ], 400);
    }

    public function getPayments(Request $request): JsonResponse
    {
        $payments = Payment::with('vendor.user', 'package')
            ->when($request->status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->when($request->method, function ($query, $method) {
                $query->where('payment_method', $method);
            })
            ->when($request->date_range, function ($query, $dateRange) {
                $dates = explode(',', $dateRange);
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($payments);
    }

    private function getRecentActivities()
    {
        return [
            [
                'message' => 'Nouveau colis créé',
                'details' => 'ECM123456 - Robe Bleue',
                'time' => 'Il y a 5 minutes',
                'type' => 'package'
            ],
            [
                'message' => 'Paiement traité',
                'details' => '45,000 FCFA - Mode Ablique X',
                'time' => 'Il y a 15 minutes',
                'type' => 'payment'
            ],
            [
                'message' => 'Nouveau vendeur inscrit',
                'details' => 'Fashion Store',
                'time' => 'Il y a 1 heure',
                'type' => 'vendor'
            ],
        ];
    }
}
