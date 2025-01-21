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
        return Inertia::render('NBRussia/Test/Index', [

        ]);
    }

    public function categories()
    {
        $categories = $this->service->parserCategories();
        return redirect()->json($categories);
    }

    public function products()
    {
        $this->service->parserProducts();

    }

}
