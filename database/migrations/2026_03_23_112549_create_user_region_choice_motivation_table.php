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
        Schema::create('user_region_choice_motivation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_region_choice_id')->constrained('user_region_choices')->onDelete('cascade');
            $table->foreignId('motivation_id')->constrained('motivations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_region_choice_motivation');
    }
};
