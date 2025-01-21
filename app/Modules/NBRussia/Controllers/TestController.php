<?php

namespace App\Modules\NBRussia\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\NBRussia\Service\TestService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestController extends Controller
{
    private TestService $testService;

    public function __construct(TestService $testService)
    {

        $this->testService = $testService;
    }

    public function index(Request $request)
    {
        return Inertia::render('NBRussia/Test/Index', [

        ]);
    }

    public function test(Request $request)
    {
        $data = $this->testService->parser();
        return redirect()->json($data);
    }
}
