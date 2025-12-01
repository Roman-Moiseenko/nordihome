<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;

class PromotionController extends ShopController
{

    private ShopRepository $repository;
    private SlugRepository $slugs;
    private MetaTemplateRepository $seo;

    public function __construct(ShopRepository $repository, SlugRepository $slugs, MetaTemplateRepository $seo)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->slugs = $slugs;
        $this->seo = $seo;
    }

    public function view(string $slug)
    {
        $promotion = $this->slugs->getPromotionBySlug($slug);
        $products = $promotion->products;
        $meta = $this->seo->seo($promotion);
        $title = $meta->title;
        $description = $meta->description;
        return view($this->route('promotion'), compact('promotion', 'products', 'title', 'description'));
    }


    public function index()
    {
        //TODO Список действующих акция ?????
    }
}
