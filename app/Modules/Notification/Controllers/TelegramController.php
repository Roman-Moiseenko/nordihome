<?php
declare(strict_types=1);

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Service\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{

    private TelegramService $service;

    public function __construct(TelegramService $service)
    {
        $this->service = $service;
    }

    public function chat_id()
    {
        $list = $this->service->getListChatIds();
        return response()->json($list);
    }

    //Принимаем вебхуки
    public function web_hook(Request $request)
    {
        try {
            if ($request->has('callback_query')) $this->service->checkOperation($request->input('callback_query'));
            if ($request->has('message')) $this->service->getMessage($request->input('message'));
        } catch (\Throwable $e) {
            Log::error(json_encode($request->all()));
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
        }

        return response('OK', 200);

    }
}
