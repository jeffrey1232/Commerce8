<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BasicTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des données de test directement avec DB::table
        DB::table('colis')->insert([
            [
                'uuid' => Str::uuid(),
                'tracking_code' => 'ECM123456',
                'vendor_id' => 1,
                'product_name' => 'Robe bleue élégante',
                'description' => 'Robe en coton, taille M',
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
                'uuid' => Str::uuid(),
                'tracking_code' => 'ECM123457',
                'vendor_id' => 1,
                'product_name' => 'Chaussures noires classiques',
                'description' => 'Chaussures en cuir, pointure 42',
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

        // Créer des paiements
        DB::table('paiements')->insert([
            [
                'uuid' => Str::uuid(),
                'colis_id' => 2, // ID du deuxième colis
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
            'name' => 'Client Test',
            'email' => 'client@ecom-best.sn',
            'password' => Hash::make('password'),
            'phone' => '+221 77 999 88 77',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Données de test créées avec succès !');
        $this->command->info('Colis créés: 2');
        $this->command->info('Paiements créés: 1');
    }
}
