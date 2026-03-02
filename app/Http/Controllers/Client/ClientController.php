<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\ValidationService;
use App\Services\PaymentService;
use App\Models\FittingRoom;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function validation()
    {
        return view('client.validation');
    }

    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cru_code' => 'required|string|exists:packages,tracking_code'
        ]);

        $package = Package::where('tracking_code', $validated['cru_code'])
            ->with('vendor.user')
            ->first();
        
        if (!$package || $package->status !== 'deposited') {
            return response()->json([
                'error' => 'Code invalide ou colis non disponible',
                'message' => 'Veuillez vérifier votre code ou contacter le service client'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'package' => $package,
            'client_info' => [
                'name' => $package->client_name,
                'product' => $package->product_name,
                'description' => $package->product_description,
                'amount' => $package->total_amount,
                'vendor' => $package->vendor->store_name,
            ],
            'tracking_code' => $package->tracking_code
        ]);
    }

    public function processPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tracking_code' => 'required|string|exists:packages,tracking_code',
            'payment_method' => 'required|in:cash,mobile_money,wave,wizall',
            'phone_number' => 'required_if:payment_method,mobile_money,wave,wizall|string',
            'use_fitting_room' => 'sometimes|boolean',
            'guarantee_type' => 'required_if:use_fitting_room,true|in:id_card,phone,cash',
            'guarantee_details' => 'required_if:use_fitting_room,true|string',
        ]);

        $paymentService = new PaymentService();
        $result = $paymentService->processClientPayment($validated);

        if ($result['success']) {
            // Gérer la cabine d'essayage si demandée
            $fittingRoomResult = null;
            if ($request->boolean('use_fitting_room')) {
                $fittingRoomResult = $this->assignFittingRoom(
                    $validated['tracking_code'],
                    $validated['guarantee_type'],
                    $validated['guarantee_details']
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement traité avec succès',
                'transaction_id' => $result['transaction_id'],
                'payment' => $result['payment'],
                'fitting_room' => $fittingRoomResult,
                'next_steps' => $this->getNextSteps($validated['payment_method'], $fittingRoomResult)
            ]);
        }

        return response()->json([
            'error' => $result['message'],
            'message' => 'Échec du traitement du paiement'
        ], 400);
    }

    public function checkPackageStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tracking_code' => 'required|string|exists:packages,tracking_code'
        ]);

        $package = Package::where('tracking_code', $validated['tracking_code'])
            ->with('vendor.user', 'payment')
            ->first();

        if (!$package) {
            return response()->json(['error' => 'Colis non trouvé'], 404);
        }

        return response()->json([
            'package' => $package,
            'status_info' => [
                'current_status' => $package->status,
                'status_label' => $this->getStatusLabel($package->status),
                'can_pay' => $package->status === 'deposited',
                'can_try_on' => $package->status === 'deposited',
                'is_paid' => $package->status === 'sold',
                'is_returned' => $package->status === 'returned',
            ]
        ]);
    }

    public function getAvailableFittingRooms(): JsonResponse
    {
        $availableRooms = FittingRoom::where('status', 'available')
            ->orderBy('name')
            ->get();

        return response()->json([
            'available_rooms' => $availableRooms,
            'total_available' => $availableRooms->count()
        ]);
    }

    public function downloadReceipt($transactionId): JsonResponse
    {
        // Implémentation pour générer et télécharger un reçu
        // Pour l'instant, retourner les informations du reçu
        
        return response()->json([
            'success' => true,
            'receipt_url' => "/receipts/{$transactionId}",
            'message' => 'Reçu généré avec succès'
        ]);
    }

    public function checkFittingStatus($trackingCode): JsonResponse
    {
        $package = Package::where('tracking_code', $trackingCode)->first();
        
        if (!$package) {
            return response()->json(['error' => 'Colis non trouvé'], 404);
        }

        $fittingRoom = FittingRoom::where('current_client', $package->client_name)
            ->where('status', 'occupied')
            ->first();

        return response()->json([
            'has_fitting' => $fittingRoom !== null,
            'fitting_room' => $fittingRoom,
            'status' => $fittingRoom ? 'occupied' : 'available'
        ]);
    }

    private function assignFittingRoom(string $trackingCode, string $guaranteeType, string $guaranteeDetails): ?array
    {
        $package = Package::where('tracking_code', $trackingCode)->first();
        if (!$package) {
            return null;
        }

        $fittingRoom = FittingRoom::where('status', 'available')->first();
        
        if (!$fittingRoom) {
            return [
                'success' => false,
                'message' => 'Aucune cabine disponible pour le moment'
            ];
        }

        $result = $fittingRoom->checkIn(
            $package->client_name,
            $guaranteeType,
            $guaranteeDetails
        );

        if ($result) {
            return [
                'success' => true,
                'fitting_room' => $fittingRoom,
                'message' => 'Cabine d\'essayage assignée avec succès',
                'instructions' => [
                    'Presentez-vous à la cabine ' . $fittingRoom->name,
                    'Votre garantie: ' . $this->getGuaranteeLabel($guaranteeType),
                    'Durée maximale: 15 minutes'
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Impossible d\'assigner la cabine d\'essayage'
        ];
    }

    private function getGuaranteeLabel(string $type): string
    {
        $labels = [
            'id_card' => 'Carte d\'identité',
            'phone' => 'Téléphone',
            'cash' => 'Espèces'
        ];

        return $labels[$type] ?? $type;
    }

    private function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'En attente de dépôt',
            'deposited' => 'Disponible au point relais',
            'sold' => 'Vendu et payé',
            'returned' => 'Retourné',
            'overdue' => 'En retard'
        ];

        return $labels[$status] ?? $status;
    }

    private function getNextSteps(string $paymentMethod, ?array $fittingRoomResult): array
    {
        $steps = [
            'Retrait de votre colis au point relais',
            'Vérification du contenu'
        ];

        if ($fittingRoomResult && $fittingRoomResult['success']) {
            $steps[] = 'Essayage dans la cabine ' . $fittingRoomResult['fitting_room']->name;
        }

        if ($paymentMethod === 'cash') {
            $steps[] = 'Paiement en espèces au point relais';
        } else {
            $steps[] = 'Paiement par ' . strtoupper($paymentMethod) . ' confirmé';
        }

        $steps[] = 'Récupération de votre reçu';

        return $steps;
    }

    public function getClientHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'email' => 'nullable|email'
        ]);

        $packages = Package::where('client_phone', $validated['phone'])
            ->when(isset($validated['email']), function ($query) use ($validated) {
                $query->whereHas('vendor.user', function ($q) use ($validated) {
                    $q->where('email', $validated['email']);
                });
            })
            ->with('vendor.user', 'payment')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'packages' => $packages,
            'total_packages' => $packages->count(),
            'total_spent' => $packages->where('status', 'sold')->sum('total_amount')
        ]);
    }
}
