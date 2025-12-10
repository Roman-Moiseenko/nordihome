<?php

use App\Modules\Page\Entity\MetaTemplate;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        MetaTemplate::register(ProductParser::class);
        MetaTemplate::register(CategoryParser::class);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        MetaTemplate::cancel(ProductParser::class);
        MetaTemplate::cancel(CategoryParser::class);
    }
};
