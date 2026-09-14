<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;


use App\Modules\Shop\Application\Queries\Group\GroupPageQuery;
use Illuminate\Http\Request;

class GroupController extends ShopController
{


    public function __construct(
        private readonly GroupPageQuery $groupPageQuery,
    )
    {
    }

    public function view(Request $request, string $slug)
    {
        $data = $this->groupPageQuery->execute(
            $slug,
            $request->all(),
            $this->getClient($request)
        );

        return view('shop.product.index', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }

}
