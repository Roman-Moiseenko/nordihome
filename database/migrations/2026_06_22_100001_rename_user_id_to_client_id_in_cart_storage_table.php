<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_storage', function (Blueprint $table) {
            try { $table->dropForeign(['user_id']); } catch (\Exception $e) {}
            try { $table->dropIndex(['user_id', 'product_id']); } catch (\Exception $e) {}
            $table->renameColumn('user_id', 'client_id');
        });

        // Удаляем записи, где client_id нет в clients — иначе FK не создастся
        DB::table('cart_storage')
            ->whereNotIn('client_id', DB::table('clients')->select('id'))
            ->delete();

        Schema::table('cart_storage', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            try { $table->index(['client_id', 'product_id']); } catch (\Exception $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('cart_storage', function (Blueprint $table) {
            try { $table->dropForeign(['client_id']); } catch (\Exception $e) {}
            try { $table->dropIndex(['client_id', 'product_id']); } catch (\Exception $e) {}
            $table->renameColumn('client_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            try { $table->index(['user_id', 'product_id']); } catch (\Exception $e) {}
        });
    }
};
