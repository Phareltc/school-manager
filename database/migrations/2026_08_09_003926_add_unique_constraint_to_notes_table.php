<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Un élève ne peut avoir qu'une seule note par examen et par matière
            $table->unique(
                ['eleve_id', 'examen_id', 'matiere_id'],
                'notes_unique_eleve_examen_matiere'
            );
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropUnique('notes_unique_eleve_examen_matiere');
        });
    }
};