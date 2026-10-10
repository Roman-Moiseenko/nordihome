<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Shop\Application\Brand\BrandPageQuery;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BrandController extends ShopAbstractController
{


    public function __construct(
        private readonly BrandPageQuery $brandPageQuery,
    )
    {
    }

    public function view(Request $request, string $slug): View|Factory|\Illuminate\View\View
    {
        $data = $this->brandPageQuery->execute($slug, $request->all(),
            $this->getClient($request));
        if (is_null($data)) abort(404, 'Бренд не найден');
        return view('shop.product.index', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }
}
