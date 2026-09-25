<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Content\Repository\MetaTemplateRepository;
use Illuminate\Contracts\View\View;

class ViewRepository
{
    private ShopRepository $repository;
    private SlugRepository $slugs;

    private MetaTemplateRepository $seo;


    public function __construct(
        ShopRepository $repository,
        SlugRepository $slugs,
        MetaTemplateRepository $seo)
    {
        $this->repository = $repository;
        $this->slugs = $slugs;
        $this->seo = $seo;
    }

    public function product_draft(string $slug): View
    {
        $product = $this->slugs->getProductBySlug($slug);
        $meta = $this->seo->seo($product);
        $title = $meta->title;
        $description = $meta->description;

        $productAttributes = $this->repository->getProdAttributes($product);
        $product = $this->repository->ProductToArrayView($product);
        $schema = "";
        return view('shop.product.view',
            compact('product', 'title', 'description', 'productAttributes', 'schema'));
    }


}
