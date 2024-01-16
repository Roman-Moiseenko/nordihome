<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Page\Entity\Page;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PageController extends Controller
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function view($slug)
    {
        $page = Page::where('slug', $slug)->first();

        return $page->view();
    }

    public function map_data(Request $request)
    {
        $map = $this->repository->getMapData($request);

        return response()->json($map);
    }

}
