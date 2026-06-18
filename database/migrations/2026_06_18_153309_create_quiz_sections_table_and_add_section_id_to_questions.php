<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Quiz;
use App\Models\QuizSection;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Créer la table quiz_sections
        Schema::create('quiz_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // 2. Ajouter section_id à quiz_questions
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('quiz_id')->constrained('quiz_sections')->nullOnDelete();
        });

        // 3. Migration de données : Créer une section par défaut pour chaque quiz existant
        // On le fait via DB pour éviter les soucis avec les modèles si ils ne sont pas encore prêts
        $quizzes = DB::table('quizzes')->get();
        foreach ($quizzes as $quiz) {
            $sectionId = DB::table('quiz_sections')->insertGetId([
                'quiz_id' => $quiz->id,
                'titre' => 'Général',
                'description' => 'Questions générales',
                'ordre' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('quiz_questions')->where('quiz_id', $quiz->id)->update([
                'section_id' => $sectionId
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
        Schema::dropIfExists('quiz_sections');
    }
};
