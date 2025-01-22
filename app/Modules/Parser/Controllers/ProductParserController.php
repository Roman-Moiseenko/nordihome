<?php

namespace App\Modules\Parser\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Repository\ProductParserRepository;
use App\Modules\Parser\Service\ProductParserService;

class ProductParserController extends Controller
{

    private ProductParserService $service;
    private ProductParserRepository $repository;

    public function __construct(
        ProductParserService $service,
        ProductParserRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        //TODO
    }
}
