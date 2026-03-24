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
        Schema::table('user_region_choice_motivation', function (Blueprint $table) {
            $table->string('motivation_libre', 255)->nullable()->after('motivation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_region_choice_motivation', function (Blueprint $table) {
            $table->dropColumn('motivation_libre');
        });
    }
};
