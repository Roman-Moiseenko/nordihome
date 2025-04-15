<?php

namespace App\Modules\Nordihome\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Nordihome\Service\FunctionService;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FunctionController extends Controller
{

    private FunctionService $service;
    private ParserIkea $parserIkea;

    public function __construct(FunctionService $service, ParserIkea $parserIkea)
    {

        $this->service = $service;
        $this->parserIkea = $parserIkea;
    }

    public function index(Request $request)
    {
        return Inertia::render('Nordihome/Function/Index', [

        ]);
    }

    public function parser_dimensions(Request $request)
    {
        try {

            $file = $this->service->parser_dimensions($request);
           // return response()->json($file);

            $headers = [
                'filename' => basename($file),
            ];
            ob_end_clean();
            ob_start();
            return response()->file($file, $headers);
        } catch (\Throwable $e) {
            return response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }
    }

    /**
     * Первоначальный парсинг всех категорий
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = $this->parserIkea->parserCategories();
            return response()->json($categories);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }

    public function furniture()
    {
        $result = $this->service->furniture();
        return response()->json($result);
    }
}
