<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vendor;
use App\Models\PointRelais;
use App\Models\Colis;
use App\Models\Client;
use App\Models\Cabine;
use App\Models\Paiement;
use App\Models\Reversement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Créer des utilisateurs de démonstration
        $this->createDemoUsers();

        // Créer des points relais (doit être avant les cabines)
        $this->createDemoPointsRelais();

        // Créer des vendeurs
        $this->createDemoVendors();

        // Créer des clients
        $this->createDemoClients();

        // Créer des cabines (après les points relais)
        $this->createDemoCabines();

        // Créer des colis
        $this->createDemoColis();

        // Créer des paiements
        $this->createDemoPaiements();

        // Créer des reversements
        $this->createDemoReversements();

        $this->command->info('✅ Données de démonstration créées avec succès');
    }

    private function createDemoUsers(): void
    {
        // Staff user
        $staff = User::firstOrCreate([
            'email' => 'staff@ecom-best.sn'
        ], [
            'name' => 'Staff ECOM-BEST',
            'phone' => '+221770000001',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        // Assigner le rôle après la création
        try {
            $staff->assignRole('staff');
        } catch (\Exception $e) {
            // Le rôle n'existe pas encore, ignorer
        }

        // Vendor user
        $vendorUser = User::firstOrCreate([
            'email' => 'vendor@ecom-best.sn'
        ], [
            'name' => 'Vendor ECOM-BEST',
            'phone' => '+221770000002',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        try {
            $vendorUser->assignRole('vendor');
        } catch (\Exception $e) {
            // Le rôle n'existe pas encore, ignorer
        }

        // Community Manager user
        $cm = User::firstOrCreate([
            'email' => 'cm@ecom-best.sn'
        ], [
            'name' => 'Community Manager',
            'phone' => '+221770000003',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        try {
            $cm->assignRole('community_manager');
        } catch (\Exception $e) {
            // Le rôle n'existe pas encore, ignorer
        }
    }

    private function createDemoPointsRelais(): void
    {
        $points = [
            [
                'name' => 'Point Relais Dakar Plateau',
                'address' => 'Avenue Bourguiba, Dakar',
                'city' => 'Dakar',
                'manager_name' => 'Aliou Sall',
                'manager_phone' => '+221770000010',
                'latitude' => 14.6928,
                'longitude' => -17.4467,
            ],
            [
                'name' => 'Point Relais Pikine',
                'address' => 'Zone Industrielle, Pikine',
                'city' => 'Pikine',
                'manager_name' => 'Fatou Binta',
                'manager_phone' => '+221770000011',
                'latitude' => 14.7435,
                'longitude' => -17.3935,
            ],
            [
                'name' => 'Point Relais Thiès',
                'address' => 'Marché Central, Thiès',
                'city' => 'Thiès',
                'manager_name' => 'Moussa Ndiaye',
                'manager_phone' => '+221770000012',
                'latitude' => 14.7833,
                'longitude' => -16.9233,
            ],
        ];

        foreach ($points as $pointData) {
            // Utiliser le modèle Eloquent pour éviter les problèmes avec softDeletes
            try {
                PointRelais::create($pointData);
            } catch (\Exception $e) {
                // Ignorer si déjà existant
            }
        }
    }

    private function createDemoVendors(): void
    {
        $vendorUser = User::where('email', 'vendor@ecom-best.sn')->first();

        $vendors = [
            [
                'business_name' => 'Fashion Dakar',
                'contact_phone' => '+221770000100',
                'contact_email' => 'contact@fashiondakar.sn',
                'address' => 'Almadies, Dakar',
                'id_card_number' => '1234567890123',
                'commission_rate' => 5.00,
            ],
            [
                'business_name' => 'Mode Sénégal',
                'contact_phone' => '+221770000101',
                'contact_email' => 'info@modesn.sn',
                'address' => 'Mermoz, Dakar',
                'id_card_number' => '9876543210987',
                'commission_rate' => 4.50,
            ],
        ];

        foreach ($vendors as $vendorData) {
            // Vérifier si le vendeur existe déjà
            $existing = DB::table('vendors')->where('business_name', $vendorData['business_name'])->first();
            if (!$existing) {
                $vendorId = DB::table('vendors')->insertGetId([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $vendorUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ] + $vendorData);

                // Assigner le rôle si possible
                try {
                    $vendorUser->assignRole('vendor');
                } catch (\Exception $e) {
                    // Ignorer si le rôle n'existe pas
                }
            }
        }
    }

    private function createDemoCabines(): void
    {
        $pointsRelais = PointRelais::withTrashed()->get();

        foreach ($pointsRelais as $point) {
            for ($i = 1; $i <= 3; $i++) {
                try {
                    Cabine::create([
                        'point_relais_id' => $point->id,
                        'name' => "Cabine {$i}",
                        'status' => 'available',
                        'fee' => 500,
                        'max_capacity' => 1,
                        'current_occupancy' => 0,
                    ]);
                } catch (\Exception $e) {
                    // Ignorer si déjà existant
                }
            }
        }
    }

    private function createDemoClients(): void
    {
        $clients = [
            ['first_name' => 'Awa', 'last_name' => 'Diop', 'phone' => '+221770000200'],
            ['first_name' => 'Mouhamed', 'last_name' => 'Ba', 'phone' => '+221770000201'],
            ['first_name' => 'Fatoumata', 'last_name' => 'Sarr', 'phone' => '+221770000202'],
            ['first_name' => 'Ibrahim', 'last_name' => 'Fall', 'phone' => '+221770000203'],
            ['first_name' => 'Mariam', 'last_name' => 'Ndiaye', 'phone' => '+221770000204'],
        ];

        foreach ($clients as $clientData) {
            try {
                Client::create($clientData);
            } catch (\Exception $e) {
                // Ignorer si déjà existant
            }
        }
    }

    private function createDemoColis(): void
    {
        $vendors = Vendor::withTrashed()->get();
        $pointsRelais = PointRelais::withTrashed()->get();
        $clients = Client::withTrashed()->get();

        $statuses = ['created', 'deposited', 'pending_withdrawal', 'in_fitting', 'paid', 'refused', 'returned'];
        $products = [
            ['Robe Bleue', 25000],
            ['Robe Rouge', 30000],
            ['Robe Verte', 22000],
            ['Robe Noire', 28000],
            ['Robe Blanche', 20000],
            ['Jupe Jaune', 15000],
            ['Jupe Rose', 18000],
            ['Chemise Orange', 12000],
        ];

        for ($i = 0; $i < 20; $i++) {
            $product = $products[array_rand($products)];
            $vendor = $vendors->random();
            $point = $pointsRelais->random();
            $client = $clients->random();

            try {
                Colis::create([
                    'tracking_code' => 'ECM' . str_pad($i + 1, 8, '0', STR_PAD_LEFT) . strtoupper(substr(uniqid(), -4)),
                    'vendor_id' => $vendor->id,
                    'client_id' => $client->id,
                    'point_relais_id' => $point->id,
                    'product_name' => $product[0],
                    'description' => 'Description du produit ' . ($i + 1),
                    'price' => $product[1],
                    'shipping_fee' => rand(500, 2000),
                    'total_amount' => $product[1] + rand(500, 2000),
                    'fitting_option' => rand(0, 1) === 1,
                    'fitting_fee' => rand(0, 1) === 1 ? 500 : 0,
                    'status' => $statuses[array_rand($statuses)],
                    'client_phone' => $client->phone,
                    'client_email' => $client->first_name . '.' . $client->last_name . '@example.com',
                    'deposited_at' => now()->subDays(rand(0, 7)),
                    'storage_deadline' => now()->addDays(rand(1, 14)),
                ]);
            } catch (\Exception $e) {
                // Ignorer les erreurs
            }
        }
    }

    private function createDemoPaiements(): void
    {
        $colis = DB::table('colis')->whereIn('status', ['paid', 'in_fitting'])->get();

        foreach ($colis as $coli) {
            $totalWithFees = $coli->total_amount + $coli->fitting_fee + ($coli->storage_fee ?? 0);

            DB::table('paiements')->insert([
                'uuid' => (string) Str::uuid(),
                'transaction_id' => 'PAY_' . time() . '_' . strtoupper(substr(uniqid(), -6)),
                'idempotency_key' => 'pay_' . $coli->id . '_' . uniqid(),
                'colis_id' => $coli->id,
                'client_id' => $coli->client_id,
                'amount' => $totalWithFees,
                'currency' => 'XOF',
                'provider' => ['wave', 'orange_money', 'mtn', 'cash'][array_rand(['wave', 'orange_money', 'mtn', 'cash'])],
                'payment_method' => 'mobile_money',
                'phone_number' => $coli->client_phone,
                'fees' => $totalWithFees * 0.02,
                'net_amount' => $totalWithFees,
                'status' => 'completed',
                'completed_at' => now()->subHours(rand(1, 24)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createDemoReversements(): void
    {
        $vendors = DB::table('vendors')->get();

        foreach ($vendors as $vendor) {
            $paidColis = DB::table('colis')
                ->where('vendor_id', $vendor->id)
                ->where('status', 'paid')
                ->get();

            foreach ($paidColis as $coli) {
                $paiement = DB::table('paiements')->where('colis_id', $coli->id)->first();
                if ($paiement) {
                    DB::table('reversements')->insert([
                        'uuid' => (string) Str::uuid(),
                        'reference' => 'REV' . date('Ym') . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                        'vendor_id' => $vendor->id,
                        'payment_id' => $paiement->id,
                        'gross_amount' => $paiement->net_amount,
                        'commission_rate' => $vendor->commission_rate,
                        'commission_amount' => $vendor->commission_rate * $paiement->net_amount / 100,
                        'net_amount' => $paiement->net_amount - ($vendor->commission_rate * $paiement->net_amount / 100),
                        'status' => ['completed', 'pending'][array_rand(['completed', 'pending'])],
                        'provider' => 'wave',
                        'recipient_phone' => $vendor->contact_phone,
                        'processed_at' => now()->subHours(rand(1, 48)),
                        'completed_at' => now()->subHours(rand(1, 24)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
