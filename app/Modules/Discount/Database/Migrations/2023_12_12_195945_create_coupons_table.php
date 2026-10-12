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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->nullable();
            $table->integer('bonus');
            $table->integer('status');
            $table->string('code', 10)->unique();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('coupons');
    }
};
