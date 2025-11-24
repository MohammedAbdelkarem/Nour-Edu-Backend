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
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('number_of_max_uses')->default(1);
            $table->dateTime('context_expired_at')->nullable();
            // for one use , context or points
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->dateTime('used_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons_migration', function (Blueprint $table) {
            //
        });
    }
};
