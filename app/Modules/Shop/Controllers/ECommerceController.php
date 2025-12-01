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
        $data = $this->repository->getDataCommerce($request);
        return \response()->json($data);
    }
}
