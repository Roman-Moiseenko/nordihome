<?php

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Shop\Application\Queries\Post\PostIndexQuery;
use App\Modules\Shop\Application\Queries\Post\PostPageQuery;

class PostController extends Controller
{

    public function __construct(
        private readonly PostPageQuery $postPageQuery,
        private readonly PostIndexQuery $postIndexQuery,
    )
    {
    }

    public function posts($slug)
    {
        $data = $this->postIndexQuery->execute($slug);

        return view('shop.content.posts', [
            'pageData' => $data,
        ]);
    }

    public function post($slug)
    {
        //FIXME после переноса на виджеты удалить
        $post = Post::where('slug', $slug)->firstOrFail();
        if ($post->old_render) return $post->view(null);

        $data = $this->postPageQuery->execute($slug);

        return view('shop.content.post', [
            'pageData' => $data,
        ]);

    }
}
