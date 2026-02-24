<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinalTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Créer manuellement les enregistrements sans utiliser les modèles
        DB::table('colis')->insert([
            [
                'uuid' => 'c533dee3-2c03-4c4d-ac76-b40848371b51',
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
                'updated_at' => now()->subDays(2),
            ],
            [
                'uuid' => 'e37dec4b-566e-415c-a61a-c401b3499e05',
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
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ]);

        // Créer les paiements
        DB::table('paiements')->insert([
            [
                'uuid' => 'f47ac10b-8a9b-4e5f-9cd6-8b2783f7c5a6',
                'colis_id' => 2,
                'amount' => 36000,
                'payment_method' => 'mobile_money',
                'phone_number' => '+221 77 234 56 78',
                'status' => 'completed',
                'paid_at' => now()->subHours(12),
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
        ]);

        // Créer un utilisateur client
        DB::table('users')->insert([
            [
                'name' => 'Client Test',
                'email' => 'client@ecom-best.sn',
                'password' => '$2y$10$92IXUNPKjOeOxmO0ECOqI6qZBuNCOyKJ7qQ', // password
                'phone' => '+221 77 999 88 77',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Données de test créées avec succès !');
        $this->command->info('Colis créés: 2');
        $this->command->info('Paiements créés: 1');
    }
}
