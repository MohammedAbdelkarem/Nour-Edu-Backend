<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PublishStatusEnum;
use App\Enums\AccessTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_level_id')->constrained('e_levels')->onDelete('cascade');
            $table->foreignId('c_level_id')->constrained('c_levels')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('name');
            $table->text('bio')->nullable();
            $table->enum('publish_status', PublishStatusEnum::values())->default(PublishStatusEnum::DRAFT->value);
            $table->integer('number_of_contents')->default(0);
            $table->integer('number_of_published_contents')->default(0);
            $table->integer('price')->default(0);
            $table->integer('number_of_purchased_students')->default(0);
            $table->enum('access_type', AccessTypeEnum::values());
            $table->integer('number_of_teachers')->default(0);
            $table->integer('number_of_quizzes')->default(0);
            $table->integer('number_of_files')->default(0);
            $table->integer('number_of_published_quizzes')->default(0);
            $table->integer('number_of_published_files')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
