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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['profil_id']);
            $table->dropColumn('profil_id');
        });

        Schema::create('quiz_profil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profil_id')->constrained('profil')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_profil');

        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('profil_id')->nullable()->constrained('profil')->nullOnDelete();
        });
    }
};
