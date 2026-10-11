<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;

use App\Modules\Cabinet\Application\Queries\PageWishQuery;
use App\Modules\Cabinet\Service\WishService;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController;
use Illuminate\Http\Request;
use function response;
use function view;

class WishController extends ShopAbstractController
{
    private WishService $service;

    public function __construct(WishService $service,
    private readonly PageWishQuery $pageWishQuery,
    )
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->pageWishQuery->execute($this->getClient($request));

        return view('cabinet.wish', ['pageData' => $data]);
    }

    //Ajax
    public function toggle(Request $request, Product $product)
    {
        $client = $this->getClient($request);
        $result = $this->service->toggle($client->id, $product->id);

        return response()->json([
            'items' => [],
            'state' => $result,
        ]);
    }

}
