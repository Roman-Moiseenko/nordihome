<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;

class GroupController extends ShopController
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
        $group = $this->slugs->getGroupBySlug($slug);
        $products = $group->products;

        $meta = $this->seo->seo($group);
        $title = $meta->title;
        $description = $meta->description;

        return view($this->route('group'), compact('group', 'products', 'title', 'description'));
    }


    public function index()
    {
        //TODO Список действующих акция ?????
    }
}
