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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_level_id')->constrained('e_levels')->onDelete('cascade');
            $table->foreignId('c_level_id')->constrained('c_levels')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('sub_unit_id')->constrained('sub_units')->onDelete('cascade');
            $table->string('name');
            $table->text('bio')->nullable();
            $table->enum('publish_status', PublishStatusEnum::values())->default(PublishStatusEnum::DRAFT->value);
            $table->integer('duration')->default(0); // in minutes
            $table->integer('priority')->default(1);
            $table->integer('number_of_quizzes')->default(0);
            $table->integer('number_of_published_quizzes')->default(0);
            $table->integer('number_of_files')->default(0);
            $table->integer('number_of_published_files')->default(0);
            $table->double('total_rate')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
