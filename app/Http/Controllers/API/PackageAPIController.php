<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Http\Resources\PackageResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackageAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $packages = Package::with('vendor.user', 'payment')
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

        return response()->json([
            'packages' => PackageResource::collection($packages->items()),
            'pagination' => [
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'per_page' => $packages->perPage(),
                'total' => $packages->total(),
                'from' => $packages->firstItem(),
                'to' => $packages->lastItem(),
            ]
        ]);
    }

    /**
     * Get package status by tracking code.
     */
    public function getStatus($trackingCode): JsonResponse
    {
        $package = Package::where('tracking_code', $trackingCode)
            ->with('vendor.user', 'payment')
            ->first();

        if (!$package) {
            return response()->json([
                'error' => 'Package not found',
                'message' => 'Ce code de suivi n\'existe pas'
            ], 404);
        }

        return response()->json([
            'package' => new PackageResource($package),
            'status_info' => [
                'current_status' => $package->status,
                'status_label' => $this->getStatusLabel($package->status),
                'can_pay' => $package->status === 'deposited',
                'can_try_on' => $package->status === 'deposited',
                'is_paid' => $package->status === 'sold',
                'is_returned' => $package->status === 'returned',
                'next_actions' => $this->getNextActions($package),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $package = Package::with('vendor.user', 'payment', 'studioSession')
            ->findOrFail($id);

        return response()->json([
            'package' => new PackageResource($package)
        ]);
    }

    /**
     * Update package status.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $package = Package::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,deposited,sold,returned,overdue',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $package->status;
        $package->status = $validated['status'];

        // Update timestamps based on status
        switch ($validated['status']) {
            case 'deposited':
                $package->deposited_at = now();
                break;
            case 'sold':
                $package->sold_at = now();
                break;
            case 'returned':
                $package->returned_at = now();
                break;
        }

        $package->save();

        return response()->json([
            'success' => true,
            'package' => new PackageResource($package->fresh()),
            'message' => 'Statut du colis mis à jour avec succès',
            'old_status' => $oldStatus,
            'new_status' => $validated['status']
        ]);
    }

    /**
     * Get package statistics.
     */
    public function getStats(Request $request): JsonResponse
    {
        $query = Package::query();

        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->date_range) {
            $dates = explode(',', $request->date_range);
            $query->whereBetween('created_at', [$dates[0], $dates[1]]);
        }

        $stats = [
            'total' => $query->count(),
            'by_status' => [
                'pending' => $query->clone()->where('status', 'pending')->count(),
                'deposited' => $query->clone()->where('status', 'deposited')->count(),
                'sold' => $query->clone()->where('status', 'sold')->count(),
                'returned' => $query->clone()->where('status', 'returned')->count(),
                'overdue' => $query->clone()->where('status', 'overdue')->count(),
            ],
            'total_value' => $query->sum('total_amount'),
            'total_commission' => $query->sum('commission_amount'),
            'total_net_amount' => $query->sum('net_amount'),
        ];

        // Add monthly trends if requested
        if ($request->include_trends) {
            $stats['monthly_trends'] = $this->getMonthlyTrends($query);
        }

        return response()->json($stats);
    }

    /**
     * Search packages.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'filters' => 'nullable|array',
            'filters.status' => 'nullable|string',
            'filters.vendor_id' => 'nullable|integer',
            'filters.date_from' => 'nullable|date',
            'filters.date_to' => 'nullable|date|after_or_equal:filters.date_from',
        ]);

        $packages = Package::with('vendor.user')
            ->where(function ($query) use ($validated) {
                $query->where('tracking_code', 'like', "%{$validated['query']}%")
                      ->orWhere('client_name', 'like', "%{$validated['query']}%")
                      ->orWhere('product_name', 'like', "%{$validated['query']}%")
                      ->orWhere('client_phone', 'like', "%{$validated['query']}%");
            })
            ->when(isset($validated['filters']['status']), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(isset($validated['filters']['vendor_id']), function ($query, $vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->when(isset($validated['filters']['date_from']), function ($query) use ($validated) {
                $query->whereDate('created_at', '>=', $validated['filters']['date_from']);
            })
            ->when(isset($validated['filters']['date_to']), function ($query) use ($validated) {
                $query->whereDate('created_at', '<=', $validated['filters']['date_to']);
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'packages' => PackageResource::collection($packages),
            'total_found' => $packages->count(),
            'query' => $validated['query']
        ]);
    }

    private function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'En attente de dépôt',
            'deposited' => 'Disponible au point relais',
            'sold' => 'Vendu et payé',
            'returned' => 'Retourné',
            'overdue' => 'En retard',
        ];

        return $labels[$status] ?? $status;
    }

    private function getNextActions(Package $package): array
    {
        $actions = [];

        switch ($package->status) {
            case 'pending':
                $actions[] = [
                    'action' => 'deposit',
                    'label' => 'Déposer au point relais',
                    'available' => true
                ];
                break;
            case 'deposited':
                $actions[] = [
                    'action' => 'pay',
                    'label' => 'Payer et récupérer',
                    'available' => true
                ];
                $actions[] = [
                    'action' => 'try_on',
                    'label' => 'Essayer en cabine',
                    'available' => true
                ];
                break;
            case 'sold':
                $actions[] = [
                    'action' => 'receipt',
                    'label' => 'Télécharger le reçu',
                    'available' => true
                ];
                break;
            case 'returned':
                $actions[] = [
                    'action' => 'contact_support',
                    'label' => 'Contacter le support',
                    'available' => true
                ];
                break;
            case 'overdue':
                $actions[] = [
                    'action' => 'contact_vendor',
                    'label' => 'Contacter le vendeur',
                    'available' => true
                ];
                break;
        }

        return $actions;
    }

    private function getMonthlyTrends($query): array
    {
        $trends = $query->selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as total,
            SUM(total_amount) as value,
            SUM(commission_amount) as commission
        ')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->take(12)
        ->get();

        return $trends->map(function ($trend) {
            return [
                'month' => $trend->month,
                'total_packages' => $trend->total,
                'total_value' => $trend->value,
                'total_commission' => $trend->commission,
            ];
        })->toArray();
    }
}
