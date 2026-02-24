<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Colis;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WorkingTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des colis de test en utilisant les modèles
        $colis1 = Colis::create([
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
        ]);

        $colis2 = Colis::create([
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
            'point_relais_id' => 1,
            'status' => 'paid',
            'product_photo' => 'https://picsum.photos/seed/shoes1/200/200.jpg',
        ]);

        // Créer un paiement pour le colis payé
        Paiement::create([
            'uuid' => Str::uuid(),
            'colis_id' => $colis2->id,
            'amount' => 36000,
            'payment_method' => 'mobile_money',
            'phone_number' => '+221 77 234 56 78',
            'status' => 'completed',
            'paid_at' => now()->subHours(12),
        ]);

        // Créer un utilisateur client
        User::create([
            'name' => 'Client Test',
            'email' => 'client@ecom-best.sn',
            'password' => Hash::make('password'),
            'phone' => '+221 77 999 88 77',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Données de test créées avec succès !');
        $this->command->info('Colis créés: 2');
        $this->command->info('Paiements créés: 1');
    }
}
