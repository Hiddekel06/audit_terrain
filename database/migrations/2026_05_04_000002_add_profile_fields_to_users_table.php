<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->after('telephone');
            $table->string('region_localite')->nullable()->after('email');
            $table->string('disponibilite')->nullable()->after('region_localite');
            $table->foreignId('profil_id')
                ->nullable()
                ->after('disponibilite')
                ->constrained('profil')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profil_id']);
            $table->dropColumn(['email', 'region_localite', 'disponibilite', 'profil_id']);
        });
    }
};