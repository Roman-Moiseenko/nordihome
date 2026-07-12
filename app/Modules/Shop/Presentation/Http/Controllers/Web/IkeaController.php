<?php

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Shop\Application\Queries\Ikea\IkeaIndexQuery;
use App\Modules\Shop\Application\Queries\Ikea\IkeaProductQuery;
use App\Modules\Shop\Application\Queries\Ikea\IkeaViewQuery;
use Illuminate\Http\Request;

class IkeaController
{

    public function __construct(
        private readonly IkeaIndexQuery $ikeaIndexQuery,
        private readonly IkeaViewQuery $ikeaViewQuery,
        private readonly IkeaProductQuery $ikeaProductQuery,
    )
    {

    }
    public function index()
    {
        $data = $this->ikeaIndexQuery->execute();

        return view('shop.ikea.index', ['pageData' => $data]);
    }

    public function view(Request $request, string $slug)
    {
        $data = $this->ikeaViewQuery->execute($slug, $request->all());

        return view('shop.ikea.view', [
            'pageData' => $data,
            'request' => $request->all(),
            ]);
    }

    public function product(Request $request, string $slug)
    {

        $data = $this->ikeaProductQuery->execute($slug);

        return view('shop.ikea.product', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }
}
