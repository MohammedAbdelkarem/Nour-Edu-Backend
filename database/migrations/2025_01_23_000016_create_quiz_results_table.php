<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\QuizResultEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('degree')->default(0);
            $table->enum('result', QuizResultEnum::values())->default(QuizResultEnum::IN_PROGRESS->value);
            $table->integer('number_of_correct_answers')->default(0);
            $table->integer('number_of_wrong_answers')->default(0);
            $table->integer('number_of_answered_questions')->default(0);
            $table->integer('taken_period')->default(0); // in minutes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};
