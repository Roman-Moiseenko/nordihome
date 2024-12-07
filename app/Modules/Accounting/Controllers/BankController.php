<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Service\BankService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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


    public function upload(Request $request): JsonResponse
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

    public function currency(Request $request): RedirectResponse
    {
        try {
            $this->service->currency($request);
            return redirect()->back()->with('success', 'Курс валют(ы) обновлен');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
