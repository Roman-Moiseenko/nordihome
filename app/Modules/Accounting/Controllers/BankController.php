<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Service\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
    private BankService $service;

    public function __construct(BankService $service)
    {
        $this->service = $service;
    }

    public function index()
    {

    }


    public function upload(Request $request)
    {
        try {
            $result = $this->service->upload($request);
            return response()->json($result);
            // return redirect()->back()->with('success', 'Сохранено');
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
            // return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
