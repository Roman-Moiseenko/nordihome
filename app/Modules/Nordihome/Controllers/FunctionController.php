<?php

namespace App\Modules\Nordihome\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Nordihome\Service\FunctionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FunctionController extends Controller
{

    private FunctionService $service;

    public function __construct(FunctionService $service)
    {

        $this->service = $service;
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
}
