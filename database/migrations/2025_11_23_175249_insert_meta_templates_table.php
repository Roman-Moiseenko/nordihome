<?php

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\MetaTemplate;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        MetaTemplate::register(Product::class);
        MetaTemplate::register(\App\Modules\Product\Entity\Category::class);
        MetaTemplate::register(Page::class);
        MetaTemplate::register(Post::class);
        MetaTemplate::register(PostCategory::class);
        MetaTemplate::register(Group::class);
        MetaTemplate::register(Promotion::class);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        MetaTemplate::cancel(Product::class);
        MetaTemplate::cancel(\App\Modules\Product\Entity\Category::class);
        MetaTemplate::cancel(Page::class);
        MetaTemplate::cancel(Post::class);
        MetaTemplate::cancel(PostCategory::class);
        MetaTemplate::cancel(Group::class);
        MetaTemplate::cancel(Promotion::class);
    }
};
