<?php

namespace App\Modules\NBRussia\Controllers;

use App\Modules\NBRussia\Service\ParserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParserController
{
    private ParserService $service;

    public function __construct(ParserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return Inertia::render('NBRussia/Parser/Index', [

        ]);
    }

    public function categories()
    {
        try {
            $categories = $this->service->parserCategories();
            return response()->json($categories);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

    }

    public function products()
    {
        $this->service->parserProducts();

    }

}
