<?php

namespace App\Modules\Shop\Controllers;

use App\Modules\Shop\Repository\ECommerceRepository;
use Illuminate\Http\Request;

class ECommerceController extends ShopController
{

    private ECommerceRepository $repository;

    public function __construct(ECommerceRepository $repository)
    {
        parent::__construct();
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
