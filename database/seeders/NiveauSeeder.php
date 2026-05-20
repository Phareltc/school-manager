<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Niveau;

class NiveauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveaux = [
            ['libelle' => '6ème'],
            ['libelle' => '5ème'],
            ['libelle' => '4ème'],
            ['libelle' => '3ème'],
            ['libelle' => 'Seconde'],
            ['libelle' => 'Première'],
            ['libelle' => 'Terminale'],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::create($niveau);
        }
    }
}
