<?php

namespace App\Modules\Page\Entity\Widgets;

use App\Modules\Page\Entity\Post;

class PostWidget extends Widget
{
    protected $table = 'widget_posts';

    public function getPost(int $count = 3)
    {
        return Post::orderByDesc('published_at')->where('published', true)->take($count);
    }
}
