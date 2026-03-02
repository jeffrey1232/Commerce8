<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Wallet;
use App\Models\Vendor;
use App\Services\PackageService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    public function dashboard()
    {
        $vendor = auth()->user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.create');
        }
        
        $stats = [
            'pending_colis' => Package::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'deposited_colis' => Package::where('vendor_id', $vendor->id)->where('status', 'deposited')->count(),
            'sold_colis' => Package::where('vendor_id', $vendor->id)->where('status', 'sold')->count(),
            'returned_colis' => Package::where('vendor_id', $vendor->id)->where('status', 'returned')->count(),
        ];

        $walletBalance = Wallet::where('vendor_id', $vendor->id)->first();
        
        $recentPackages = Package::where('vendor_id', $vendor->id)
            ->with('payment')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('vendor.dashboard', compact('stats', 'walletBalance', 'recentPackages'));
    }

    public function createShipment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string',
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $vendor = auth()->user()->vendor;
        
        if (!$vendor) {
            return response()->json(['error' => 'Vendeur non trouvé'], 404);
        }

        $packageService = new PackageService();
        $package = $packageService->createPackage($validated, $vendor);

        return response()->json([
            'success' => true,
            'package' => $package,
            'tracking_code' => $package->tracking_code,
            'message' => 'Colis créé avec succès'
        ]);
    }

    public function wallet(): JsonResponse
    {
        $vendor = auth()->user()->vendor;
        
        if (!$vendor) {
            return response()->json(['error' => 'Vendeur non trouvé'], 404);
        }

        $wallet = Wallet::where('vendor_id', $vendor->id)->first();
        
        if (!$wallet) {
            $wallet = Wallet::create([
                'vendor_id' => $vendor->id,
                'balance' => 0,
                'pending_balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
            ]);
        }

        $recentTransactions = Package::where('vendor_id', $vendor->id)
            ->where('status', 'sold')
            ->with('payment')
            ->orderBy('sold_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'wallet' => $wallet,
            'recent_transactions' => $recentTransactions,
            'stats' => [
                'total_earned' => $wallet->total_earned,
                'available_balance' => $wallet->balance,
                'pending_balance' => $wallet->pending_balance,
                'total_withdrawn' => $wallet->total_withdrawn,
            ]
        ]);
    }

    public function packages(Request $request): JsonResponse
    {
        $vendor = auth()->user()->vendor;
        
        if (!$vendor) {
            return response()->json(['error' => 'Vendeur non trouvé'], 404);
        }

        $packages = Package::where('vendor_id', $vendor->id)
            ->with('payment')
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('tracking_code', 'like', "%{$search}%")
                      ->orWhere('client_name', 'like', "%{$search}%")
                      ->orWhere('product_name', 'like', "%{$search}%");
            })
            ->when($request->date_range, function ($query, $dateRange) {
                $dates = explode(',', $dateRange);
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($packages);
    }

    public function show($id): JsonResponse
    {
        $vendor = auth()->user()->vendor;
        
        if (!$vendor) {
            return response()->json(['error' => 'Vendeur non trouvé'], 404);
        }

        $package = Package::where('vendor_id', $vendor->id)
            ->with('payment', 'studioSession')
            ->findOrFail($id);

        return response()->json($package);
    }

    public function updatePackage(Request $request, $id): JsonResponse
    {
        $vendor = auth()->user()->vendor;
        
        if (!$vendor) {
            return response()->json(['error' => 'Vendeur non trouvé'], 404);
        }

        $package = Package::where('vendor_id', $vendor->id)->findOrFail($id);

        $validated = $request->validate([
            'client_name' => 'sometimes|string|max:255',
            'client_phone' => 'sometimes|string|max:20',
            'product_name' => 'sometimes|string|max:255',
            'product_description' => 'sometimes|string',
            'total_amount' => 'sometimes|numeric|min:0',
        ]);

        $package->update($validated);

        // Recalculer commission si montant changé
        if (isset($validated['total_amount'])) {
            $commission = $validated['total_amount'] * ($vendor->commission_rate / 100);
            $package->commission_amount = $commission;
            $package->net_amount = $validated['total_amount'] - $commission;
            $package->save();
        }

        return response()->json([
            'success' => true,
            'package' => $package->fresh(),
            'message' => 'Colis mis à jour avec succès'
        ]);
    }

    public function getStats(Request $request): JsonResponse
    {
        $vendor = auth()->user()->vendor;
        
        if (!$vendor) {
            return response()->json(['error' => 'Vendeur non trouvé'], 404);
        }

        $packageService = new PackageService();
        $stats = $packageService->getPackageStats(['vendor_id' => $vendor->id]);

        $wallet = Wallet::where('vendor_id', $vendor->id)->first();

        return response()->json([
            'packages' => $stats,
            'wallet' => [
                'balance' => $wallet ? $wallet->balance : 0,
                'pending_balance' => $wallet ? $wallet->pending_balance : 0,
                'total_earned' => $wallet ? $wallet->total_earned : 0,
            ],
            'vendor_info' => [
                'store_name' => $vendor->store_name,
                'commission_rate' => $vendor->commission_rate,
                'rating' => $vendor->rating,
                'total_packages' => $vendor->total_packages,
                'total_revenue' => $vendor->total_revenue,
            ]
        ]);
    }

    public function create(): \Illuminate\View\View
    {
        $user = auth()->user();
        
        if ($user->vendor) {
            return redirect()->route('vendor.dashboard');
        }

        return view('vendor.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
            'business_license' => 'nullable|string|max:255',
        ]);

        $vendor = Vendor::create([
            'user_id' => auth()->id(),
            'store_name' => $validated['store_name'],
            'store_address' => $validated['store_address'],
            'store_phone' => $validated['store_phone'],
            'business_license' => $validated['business_license'] ?? null,
            'commission_rate' => 5.00, // Taux par défaut
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

        return response()->json([
            'success' => true,
            'vendor' => $vendor,
            'message' => 'Boutique créée avec succès'
        ]);
    }
}
