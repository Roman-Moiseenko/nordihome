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
            $this->service->checkOperation($request->all());
        } catch (\Throwable $e) {
            Log::error($request->all());
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
        }

        return response('OK', 200);

    }
}
