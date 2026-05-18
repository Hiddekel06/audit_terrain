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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            } else {
                // Si la colonne existe déjà (échec migration précédente), on ajoute juste la contrainte si nécessaire
                // Mais le plus simple est de s'assurer qu'on peut la recréer proprement ou juste l'ignorer si déjà là avec contrainte
                // Pour éviter les erreurs, on va juste s'assurer que si elle est là, on ne fait rien ou on ajoute la FK.
                // En Laravel, ajouter une FK sur une colonne existante se fait séparément.
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
