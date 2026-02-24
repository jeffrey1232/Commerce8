<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;
use App\Models\Paiement;
use App\Models\Essai;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        return view('client.validation');
    }

    public function validateCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|min:6|max:10'
        ]);

        // Rechercher le colis par code de suivi
        $colis = Colis::where('tracking_code', $validated['code'])
            ->with(['vendor', 'pointRelais'])
            ->first();

        if (!$colis) {
            return response()->json([
                'success' => false,
                'message' => 'Code CRU invalide'
            ], 404);
        }

        // Vérifier si le colis peut être validé
        if (!in_array($colis->status, ['deposited', 'pending_withdrawal'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ce colis ne peut pas être validé à ce stade'
            ]);
        }

        return response()->json([
            'success' => true,
            'colis' => [
                'uuid' => $colis->uuid,
                'tracking_code' => $colis->tracking_code,
                'product_name' => $colis->product_name,
                'description' => $colis->description ?? '',
                'price' => $colis->price,
                'total_amount' => $colis->total_amount,
                'vendor_name' => $colis->vendor->name,
                'point_relais' => $colis->pointRelais->name,
                'address' => $colis->pointRelais->address,
                'status' => $colis->status,
                'image_url' => $colis->image_url ?? 'https://picsum.photos/seed/product1/200/200.jpg'
            ]
        ]);
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'colis_uuid' => 'required|exists:colis,uuid',
            'payment_method' => 'required|string|in:cash,mobile_money',
            'phone_number' => 'required_if:payment_method,mobile_money|string',
            'fitting_option' => 'boolean'
        ]);

        $colis = Colis::where('uuid', $validated['colis_uuid'])->firstOrFail();

        // Vérifier que le colis peut être payé
        if (!in_array($colis->status, ['deposited', 'pending_withdrawal', 'in_fitting'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ce colis ne peut pas être payé à ce stade'
            ]);
        }

        // Créer le paiement
        $paiement = Paiement::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'colis_id' => $colis->id,
            'amount' => $colis->total_amount,
            'payment_method' => $validated['payment_method'],
            'phone_number' => $validated['phone_number'] ?? null,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Mettre à jour le statut du colis
        $newStatus = $validated['fitting_option'] ? 'in_fitting' : 'paid';
        $colis->update(['status' => $newStatus]);

        // Si option d'essayage, créer l'essai
        if ($validated['fitting_option']) {
            Essai::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'colis_id' => $colis->id,
                'client_phone' => $validated['phone_number'],
                'status' => 'scheduled',
                'scheduled_at' => now()->addMinutes(30),
                'cabine_id' => 1, // Première cabine disponible
            ]);
        }

        // Générer un numéro de reçu
        $receiptNumber = 'RCT-' . date('Y') . '-' . str_pad(Paiement::count(), 6, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'message' => 'Paiement traité avec succès',
            'receipt_number' => $receiptNumber,
            'colis_status' => $newStatus,
            'fitting_scheduled' => $validated['fitting_option']
        ]);
    }

    public function downloadReceipt($receiptNumber)
    {
        // Logique pour générer et télécharger le PDF du reçu
        return response()->json([
            'success' => true,
            'message' => 'Téléchargement du reçu ' . $receiptNumber
        ]);
    }

    public function checkFittingStatus($colisUuid)
    {
        $essai = Essai::whereHas('colis', function($query) use ($colisUuid) {
            $query->where('uuid', $colisUuid);
        })->with('cabine')->first();

        if (!$essai) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun essai trouvé pour ce colis'
            ]);
        }

        return response()->json([
            'success' => true,
            'essai' => [
                'status' => $essai->status,
                'cabine_name' => $essai->cabine->name ?? 'Non assignée',
                'scheduled_at' => $essai->scheduled_at->format('d/m/Y H:i'),
                'started_at' => $essai->started_at?->format('d/m/Y H:i'),
                'completed_at' => $essai->completed_at?->format('d/m/Y H:i')
            ]
        ]);
    }
}
