<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PublishStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->morphs('context');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->integer('priority')->default(1);
            $table->integer('period')->default(0); // in minutes
            $table->integer('number_of_questions')->default(0);
            $table->enum('publish_status', PublishStatusEnum::values())->default(PublishStatusEnum::DRAFT->value);
            $table->integer('degree')->default(0); // total possible score
            $table->integer('pass_degree')->default(0); // passing score
            $table->double('one_question_degree')->default(0); // one question degree
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
