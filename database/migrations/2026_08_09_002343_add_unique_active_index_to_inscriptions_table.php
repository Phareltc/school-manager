<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Laravel's Schema Builder ne permet pas de créer un index unique CONDITIONNEL
        // (WHERE statut = 'actif'), donc on passe par du SQL brut, spécifique à PostgreSQL.
        DB::statement("
            CREATE UNIQUE INDEX inscriptions_unique_actif
            ON inscriptions (eleve_id, annee_scolaire_id)
            WHERE statut = 'actif'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS inscriptions_unique_actif');
    }
};