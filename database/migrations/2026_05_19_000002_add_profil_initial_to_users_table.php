<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('profil_initial_id')->nullable()->after('profil_id');
            $table->foreign('profil_initial_id')->references('id')->on('profil')->nullOnDelete();
        });

        // Backfill existing users with their current profil as initial choice
        DB::table('users')->whereNull('profil_initial_id')->update([
            'profil_initial_id' => DB::raw('profil_id')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profil_initial_id']);
            $table->dropColumn('profil_initial_id');
        });
    }
};
