<?php

use App\Modules\Page\Entity\MetaTemplate;
use App\Modules\Parser\Entity\ParserProduct;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        MetaTemplate::register(ParserProduct::class);
        MetaTemplate::register(ParserCategory::class);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        MetaTemplate::cancel(ParserProduct::class);
        MetaTemplate::cancel(ParserCategory::class);
    }
};
