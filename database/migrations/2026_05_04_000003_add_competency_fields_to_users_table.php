<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('niveau_numerique')->nullable()->after('profil_id');
            $table->json('experiences')->nullable()->after('niveau_numerique');
            $table->json('competences_techniques')->nullable()->after('experiences');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'niveau_numerique',
                'experiences',
                'competences_techniques',
            ]);
        });
    }
};