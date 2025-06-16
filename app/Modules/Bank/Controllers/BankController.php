<?php

namespace App\Modules\Bank\Controllers;

use App\Modules\Bank\Service\BankService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BankController
{

    private BankService $service;


    public function __construct(BankService $service)
    {
        $this->service = $service;
    }

    /** Загрузка файла в формате 1С */
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

    /** Обновление курса валют */
    public function currency(Request $request): RedirectResponse
    {
        try {
            $this->service->currency($request);
            return redirect()->back()->with('success', 'Курс валют(ы) обновлен');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /** Прием данных по API из банков */
    public function web_hook(Request $request)
    {
        //Проверяем, тип платежа и формируем чек, если succeed. Отправляем чек клиенту

        return \response()->json([true]);
    }

    public function redirect(Request $request): RedirectResponse
    {

    }


}
