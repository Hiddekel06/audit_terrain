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
        Schema::create('user_dynamic_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dynamic_question_id')->constrained('dynamic_questions')->onDelete('cascade');
            $table->foreignId('dynamic_question_option_id')->nullable()->constrained('dynamic_question_options')->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'dynamic_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dynamic_answers');
    }
};
