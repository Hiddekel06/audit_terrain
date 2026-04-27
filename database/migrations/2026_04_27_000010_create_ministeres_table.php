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
        Schema::create('ministeres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('departement_id')->nullable();

            $table->index(['nom', 'id', 'parent_id'], 'idx_ministeres_nom_covering');
            $table->index('nom', 'idx_ministeres_nom');
            $table->index('parent_id', 'idx_ministeres_parent_id');
            $table->index('region_id', 'idx_ministeres_region_id');
            $table->index('departement_id', 'idx_ministeres_departement_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministeres');
    }
};
