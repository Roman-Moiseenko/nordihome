<?php

use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        DB::table('brands')->orderBy('id')->get()->each(function ($brand) {
            try {
                $slug = (string) new Slug($brand->name);
            } catch (\InvalidArgumentException) {
                $slug = 'brand-' . $brand->id;
            }

            $base = $slug;
            $i = 1;
            while (DB::table('brands')
                ->where('slug', $slug)
                ->where('id', '!=', $brand->id)
                ->exists()) {
                $slug = $base . '-' . (++$i);
            }

            DB::table('brands')->where('id', $brand->id)->update(['slug' => $slug]);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
