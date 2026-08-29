<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Content\Repository\MetaTemplateRepository;
use App\Modules\Shop\Application\Queries\Promotion\PromotionPageQuery;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PromotionController extends ShopController
{

    private ShopRepository $repository;
    private SlugRepository $slugs;
    private MetaTemplateRepository $seo;

    public function __construct(
        ShopRepository             $repository,
        SlugRepository             $slugs,
        MetaTemplateRepository     $seo,
        private PromotionPageQuery $promotionPageQuery,
    )
    {
        // parent::__construct();
        $this->repository = $repository;
        $this->slugs = $slugs;
        $this->seo = $seo;
    }

    public function view(Request $request, string $slug): View|Factory|\Illuminate\View\View
    {
        $data = $this->promotionPageQuery->execute($slug, $request->all(),
            $this->getClient($request));

        return view('shop.product.index', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }


    public function index()
    {
        //TODO Список действующих акция ?????
    }
}
