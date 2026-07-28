<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset du cache de permissions (important après un seed)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // -----------------------
        // 1. Permissions atomiques
        // -----------------------
        $permissions = [
            'view-dashboard',
            'log-calories',
            'log-weight',
            'view-stock',
            'manage-stock',        // ajouter/modifier/supprimer des plats
            'consume-stock',       // manger une portion
            'view-targets',
            'manage-targets',      // créer/supprimer des objectifs caloriques
            'view-settings',
            'manage-settings',     // logo, nom, titre, urls
            'manage-users',        // gérer les comptes et rôles
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // -----------------------
        // 2. Rôles avec leurs permissions
        // -----------------------
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions); // tout

        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions([
            'view-dashboard',
            'log-calories',
            'log-weight',
            'view-stock',
            'manage-stock',
            'manage-targets',
            'consume-stock',
            'view-targets',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view-dashboard',
            'view-stock',
            'view-targets',
        ]);

        // -----------------------
        // 3. Utilisateur admin par défaut
        // -----------------------
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@caltracker.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('changeme123'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['admin']);

        $this->command->info('Rôles admin / editor / viewer créés.');
        $this->command->info('Admin : admin@caltracker.local / changeme123');
    }
}
