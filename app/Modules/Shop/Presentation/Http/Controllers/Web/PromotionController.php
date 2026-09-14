<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Shop\Application\Queries\Promotion\PromotionPageQuery;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PromotionController extends ShopController
{


    public function __construct(
        private readonly PromotionPageQuery $promotionPageQuery,
    )
    {
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
}
