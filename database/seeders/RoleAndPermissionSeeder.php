<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Création des permissions
        $permissions = [
            // Gestion vendeurs
            'vendors.view',
            'vendors.create',
            'vendors.edit',
            'vendors.delete',
            'vendors.approve',
            'vendors.reject',
            'vendors.manage_balance',

            // Gestion clients
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'clients.verify',

            // Gestion points relais
            'points_relais.view',
            'points_relais.create',
            'points_relais.edit',
            'points_relais.delete',
            'points_relais.manage_storage',

            // Gestion colis
            'colis.view',
            'colis.create',
            'colis.edit',
            'colis.delete',
            'colis.deposit',
            'colis.withdraw',
            'colis.change_status',
            'colis.view_logs',

            // Gestion paiements
            'paiements.view',
            'paiements.process',
            'paiements.refund',
            'paiements.verify_webhook',

            // Gestion reversements
            'reversements.view',
            'reversements.process',
            'reversements.approve',
            'reversements.batch_process',

            // Gestion cabines et essais
            'cabines.view',
            'cabines.create',
            'cabines.edit',
            'cabines.delete',
            'essais.view',
            'essais.create',
            'essais.complete',
            'essais.cancel',

            // Gestion notifications
            'notifications.view',
            'notifications.send',
            'notifications.retry',

            // Gestion services digitaux
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'services.toggle',

            // Gestion logs système
            'logs.view',
            'logs.export',
            'logs.security',

            // Administration système
            'dashboard.view',
            'statistics.view',
            'settings.manage',
            'users.manage',
            'roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Création des rôles
        $roles = [
            'admin' => [
                'description' => 'Administrateur système - Accès complet',
                'permissions' => Permission::all()->pluck('name')->toArray(),
            ],
            'vendor' => [
                'description' => 'Vendeur - Gestion de ses produits et ventes',
                'permissions' => [
                    'colis.view',
                    'colis.create',
                    'colis.edit',
                    'colis.delete',
                    'colis.deposit',
                    'colis.view_logs',
                    'paiements.view',
                    'reversements.view',
                    'notifications.view',
                    'dashboard.view',
                ],
            ],
            'staff' => [
                'description' => 'Staff point relais - Gestion opérationnelle',
                'permissions' => [
                    'colis.view',
                    'colis.deposit',
                    'colis.withdraw',
                    'colis.change_status',
                    'colis.view_logs',
                    'clients.view',
                    'clients.verify',
                    'cabines.view',
                    'cabines.edit',
                    'essais.view',
                    'essais.create',
                    'essais.complete',
                    'essais.cancel',
                    'paiements.view',
                    'paiements.process',
                    'notifications.view',
                    'notifications.send',
                    'points_relais.manage_storage',
                    'dashboard.view',
                ],
            ],
            'community_manager' => [
                'description' => 'Community Manager - Support et communication',
                'permissions' => [
                    'clients.view',
                    'clients.edit',
                    'vendors.view',
                    'colis.view',
                    'notifications.view',
                    'notifications.send',
                    'logs.view',
                    'dashboard.view',
                    'statistics.view',
                ],
            ],
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);

            $role->givePermissionTo($roleData['permissions']);
        }

        // Création utilisateur admin par défaut
        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@ecom-best.sn'
        ], [
            'name' => 'Administrateur ECOM-BEST',
            'phone' => '+221770000000',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        $this->command->info('✅ Rôles et permissions créés avec succès');
        $this->command->info('👤 Utilisateur admin créé: admin@ecom-best.sn / password');
    }
}
