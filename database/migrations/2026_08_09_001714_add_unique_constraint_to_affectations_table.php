<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
            // Empêche en base tout doublon enseignant/classe/matiere/annee_scolaire
            $table->unique(
                ['enseignant_id', 'classe_id', 'matiere_id', 'annee_scolaire_id'],
                'affectations_unique_combinaison'
            );
        });
    }

    public function down(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
            $table->dropUnique('affectations_unique_combinaison');
        });
    }
};