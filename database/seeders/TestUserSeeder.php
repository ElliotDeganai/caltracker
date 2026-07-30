<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $viewer = User::updateOrCreate(
            ['email' => 'viewer@caltracker.local'],
            [
                'name' => 'Utilisateur Test',
                'password' => Hash::make('viewer123'),
                'email_verified_at' => now(),
            ]
        );
        $viewer->syncRoles(['viewer']);

        $editor = User::updateOrCreate(
            ['email' => 'editor@caltracker.local'],
            [
                'name' => 'Éditeur Test',
                'password' => Hash::make('editor123'),
                'email_verified_at' => now(),
            ]
        );
        $editor->syncRoles(['editor']);

        $this->command->info('Viewer : viewer@caltracker.local / viewer123');
        $this->command->info('Editor : editor@caltracker.local / editor123');
    }
}
