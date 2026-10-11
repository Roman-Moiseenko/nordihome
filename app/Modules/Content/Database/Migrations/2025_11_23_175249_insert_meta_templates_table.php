<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      /*  MetaTemplate::register(Product::class);
        MetaTemplate::register(Category::class);
        MetaTemplate::register(Page::class);
        MetaTemplate::register(Post::class);
        MetaTemplate::register(PostCategory::class);
        MetaTemplate::register(Group::class);
        MetaTemplate::register(Promotion::class);
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      /*  MetaTemplate::cancel(Product::class);
        MetaTemplate::cancel(Category::class);
        MetaTemplate::cancel(Page::class);
        MetaTemplate::cancel(Post::class);
        MetaTemplate::cancel(PostCategory::class);
        MetaTemplate::cancel(Group::class);
        MetaTemplate::cancel(Promotion::class);*/
    }
};
