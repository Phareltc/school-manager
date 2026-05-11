<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->foreignId('examen_id')->constrained('examens')->cascadeOnDelete();            
            $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();            
            $table->foreignId('enseignant_id')->constrained('enseignants')->cascadeOnDelete();
            $table->decimal('note', 4, 2);
            $table->string('commentaire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
