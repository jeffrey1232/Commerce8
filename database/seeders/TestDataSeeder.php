<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Colis;
use App\Models\Paiement;
use App\Models\Essai;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Créer quelques colis de test
        $colis = [
            [
                'uuid' => Str::uuid(),
                'tracking_code' => 'ECM123456',
                'vendor_id' => 1,
                'product_name' => 'Robe bleue élégante',
                'description' => 'Robe en coton, taille M, parfaite pour occasions spéciales',
                'price' => 25000,
                'shipping_fee' => 1000,
                'fitting_fee' => 500,
                'total_amount' => 26500,
                'client_phone' => '+221 77 123 45 67',
                'client_email' => 'client1@example.com',
                'point_relais_id' => 1,
                'status' => 'deposited',
                'product_photo' => 'https://picsum.photos/seed/dress1/200/200.jpg',
                'created_at' => now()->subDays(2),
            ],
            [
                'uuid' => Str::uuid(),
                'tracking_code' => 'ECM123457',
                'vendor_id' => 1,
                'product_name' => 'Chaussures noires classiques',
                'description' => 'Chaussures en cuir, pointure 42, élégantes et confortables',
                'price' => 35000,
                'shipping_fee' => 1000,
                'fitting_fee' => 0,
                'total_amount' => 36000,
                'client_phone' => '+221 77 234 56 78',
                'client_email' => 'client2@example.com',
                'point_relais_id' => 2,
                'status' => 'paid',
                'product_photo' => 'https://picsum.photos/seed/shoes1/200/200.jpg',
                'created_at' => now()->subDays(1),
            ],
            [
                'uuid' => Str::uuid(),
                'tracking_code' => 'ECM123458',
                'vendor_id' => 2,
                'product_name' => 'Sac à main en cuir',
                'description' => 'Sac en cuir véritable, couleur marron, spacieux et élégant',
                'price' => 18000,
                'shipping_fee' => 1000,
                'fitting_fee' => 500,
                'total_amount' => 19500,
                'client_phone' => '+221 76 345 67 89',
                'client_email' => 'client3@example.com',
                'point_relais_id' => 2,
                'status' => 'pending_withdrawal',
                'product_photo' => 'https://picsum.photos/seed/bag1/200/200.jpg',
                'created_at' => now()->subHours(6),
            ],
            [
                'uuid' => Str::uuid(),
                'tracking_code' => 'ECM123459',
                'vendor_id' => 2,
                'product_name' => 'Monte en soie',
                'description' => 'Monte en soie légère, couleur rouge, élégant et moderne',
                'price' => 42000,
                'shipping_fee' => 1000,
                'fitting_fee' => 500,
                'total_amount' => 43500,
                'client_phone' => '+221 77 456 78 90',
                'client_email' => 'client4@example.com',
                'point_relais_id' => 2,
                'status' => 'in_fitting',
                'product_photo' => 'https://picsum.photos/seed/shirt1/200/200.jpg',
                'created_at' => now()->subHours(3),
            ],
        ];

        foreach ($colis as $colisData) {
            Colis::create($colisData);
        }

        // Créer des paiements pour les colis payés
        $paidColis = Colis::where('status', 'paid')->get();
        foreach ($paidColis as $colis) {
            Paiement::create([
                'uuid' => Str::uuid(),
                'colis_id' => $colis->id,
                'amount' => $colis->total_amount,
                'payment_method' => 'mobile_money',
                'phone_number' => $colis->client_phone,
                'status' => 'completed',
                'paid_at' => now()->subHours(rand(1, 24)),
            ]);
        }

        // Créer des essais pour les colis en essayage
        $fittingColis = Colis::where('status', 'in_fitting')->get();
        foreach ($fittingColis as $colis) {
            Essai::create([
                'uuid' => Str::uuid(),
                'colis_id' => $colis->id,
                'client_phone' => $colis->client_phone,
                'status' => 'in_progress',
                'cabine_id' => 1,
                'started_at' => now()->subMinutes(30),
                'scheduled_at' => now()->subMinutes(60),
            ]);
        }

        // Créer un utilisateur client de test
        User::create([
            'name' => 'Client Test',
            'email' => 'client@ecom-best.sn',
            'password' => Hash::make('password'),
            'phone' => '+221 77 999 88 77',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Données de test créées avec succès !');
        $this->command->info('Colis créés: ' . count($colis));
        $this->command->info('Paiements créés: ' . $paidColis->count());
        $this->command->info('Essais créés: ' . $fittingColis->count());
    }
}
