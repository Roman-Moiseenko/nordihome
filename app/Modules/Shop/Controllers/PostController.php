<?php

namespace App\Modules\Shop\Controllers;

use App\Modules\Content\Repository\MetaTemplateRepository;
use App\Modules\Shop\Application\Queries\Post\PostPageQuery;
use App\Modules\Shop\Repository\SlugRepository;
use App\Modules\Shop\Repository\ViewRepository;

class PostController extends ShopController
{
    private ViewRepository $views;

    public function __construct(
        ViewRepository $views,
        private SlugRepository $slugs,
        private MetaTemplateRepository $seo,
        private readonly PostPageQuery $postPageQuery,
    )
    {
        parent::__construct();
        $this->views = $views;
    }

    public function posts($slug)
    {

        $posts = $this->slugs->PostCategoryBySlug($slug);

        return $posts->view($this->seo->seoFn());

        //return $this->views->posts($slug);
    }

    public function post($slug)
    {
        $data = $this->postPageQuery->execute($slug);

        return view('', [
            'pageData' => $data,
        ]);

        //return $this->views->post($slug);
    }
}
