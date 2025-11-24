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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->default(2)->constrained('roles'); // 1 => Super Admin / 2 => admin / 3=> doctor / 4=> patient
            $table->string('name')->nullable(); // nullable to fill them in the third screen (profile info)
            $table->string('phone_number')->nullable();
            $table->text('bio')->nullable();
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_male')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('language')->default('en');
            $table->boolean('active_notifications')->default(true);
            $table->dateTime('deactive_at')->nullable();
            $table->timestamp('account_verified_at')->nullable();
            $table->foreignId('contry_id')->nullable()->constrained('contries')->nullOnDelete();
            $table->foreignId('e_level_id')->nullable()->constrained('e_levels')->nullOnDelete();
            $table->foreignId('c_level_id')->nullable()->constrained('c_levels')->nullOnDelete();
            $table->integer('balance')->default(0);
            $table->integer('study_minutes')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};