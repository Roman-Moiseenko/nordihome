<?php

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;


use App\Modules\Shop\Application\Queries\Room\RoomIndexQuery;
use App\Modules\Shop\Application\Queries\Room\RoomPageQuery;
use Illuminate\Http\Request;

class RoomController extends ShopController
{
    public function __construct(
        private readonly RoomPageQuery $roomPageQuery,
        private readonly RoomIndexQuery $roomIndexQuery,
    )
    {
    }

    public function index()
    {

        $data = $this->roomIndexQuery->execute();

        return view('shop.catalog.room', [
            'pageData' => $data,
        ]);
    }

    public function view(Request $request, string $slug)
    {

        $data = $this->roomPageQuery->execute(
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
