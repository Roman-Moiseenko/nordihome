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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('banned_at')->nullable();
            $table->string('profileable_type')->nullable();
            $table->bigInteger('profileable_id')->nullable();


            $table->dropColumn('phone');
            $table->dropColumn('client');
            $table->dropColumn('fullname');
            $table->dropColumn('address');
            $table->dropColumn('delivery');
            $table->dropColumn('storage');
            $table->dropColumn('legal');
            $table->dropColumn('active');
            $table->dropColumn('agree');
        });
        Schema::table('users', function (Blueprint $table) {
           $table->dropForeign(['organization_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('banned_at');
            $table->dropColumn('profileable_type');
            $table->dropColumn('profileable_id');


            $table->string('phone')->default('');
            $table->integer('client')->nullable();
            $table->json('fullname')->nullable();
            $table->json('address')->nullable();
            $table->integer('delivery')->nullable();
            $table->integer('storage')->nullable();
            $table->boolean('legal')->default(false);;
            $table->boolean('active')->default(false);;
            $table->boolean('agree')->default(false);
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
        });

    }
};
