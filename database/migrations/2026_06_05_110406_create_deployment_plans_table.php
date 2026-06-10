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
        Schema::create('deployment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->json('data'); // Structure des équipes et IDs des membres
            $table->json('summary')->nullable(); // Stats (équipes, membres, etc.)
            $table->json('metadata')->nullable(); // Quotas, filtres région, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_plans');
    }
};
