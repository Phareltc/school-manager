<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Création des permissions
        $gererNotes = Permission::firstOrCreate(['name' => 'gerer-notes']);
        $gererEleves = Permission::firstOrCreate(['name' => 'gerer-eleves']);
        $gererInscriptions = Permission::firstOrCreate(['name' => 'gerer-inscriptions']);
        $voirBulletins = Permission::firstOrCreate(['name' => 'voir-bulletins']);

        // Attribution des permissions aux rôles
        $admin = Role::where('name', 'admin')->first();
        $enseignant = Role::where('name', 'enseignant')->first();

        // L'admin a TOUTES les permissions
        $admin->givePermissionTo([$gererNotes, $gererEleves, $gererInscriptions, $voirBulletins]);

        // L'enseignant a un accès plus limité, centré sur son métier
        $enseignant->givePermissionTo([$gererNotes, $voirBulletins]);
    }
}