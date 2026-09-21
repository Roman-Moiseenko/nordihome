<?php

namespace App\Modules\Output\Presentation\Http\Controllers\Web;

use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use App\Modules\Shop\Repository\ECommerceRepository;
use Illuminate\Http\Request;

class ECommerceController extends ShopController
{

    private ECommerceRepository $repository;

    public function __construct(ECommerceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function e_commerce(Request $request): \Illuminate\Http\JsonResponse
    {
        //FIXME Заменить через Query

        $data = $this->repository->getDataCommerce($request);
        //$data = [];
        return \response()->json($data);
    }
}
