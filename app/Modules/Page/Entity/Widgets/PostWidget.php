<?php

namespace App\Modules\Page\Entity\Widgets;

use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $category_id
 * @property PostCategory $category
 */
class PostWidget extends Widget
{
    protected $table = 'widget_posts';

    public function getPost(int $count = 3)
    {
        $query = Post::orderByDesc('published_at')->where('published', true);
        if (!is_null($this->category_id)) {
            $query->where('category_id', $this->category_id);
        }
        return $query->take($count)->getModels();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }
}
