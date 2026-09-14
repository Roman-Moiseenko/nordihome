<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Content\Entity\News;
use App\Modules\Content\Entity\PostCategory;
use App\Modules\Content\Entity\Widgets\Template;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Content\Repository\MetaTemplateRepository;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\Deprecated;

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
