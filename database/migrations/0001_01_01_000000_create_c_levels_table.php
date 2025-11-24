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
        Schema::create('c_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_level_id')->constrained('e_levels')->onDelete('cascade');
            $table->string('name');
            $table->text('bio')->nullable();
            $table->enum('publish_status', PublishStatusEnum::values())->default(PublishStatusEnum::DRAFT->value);
            $table->integer('number_of_contents')->default(0);
            $table->integer('number_of_published_contents')->default(0);
            $table->integer('number_of_students')->default(0);
            $table->integer('number_of_teachers')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_levels');
    }
};
