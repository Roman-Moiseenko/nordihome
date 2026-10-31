<?php

declare(strict_types=1);

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Setting\Application\Actions\ClearAllCacheUseCase;
use App\Modules\Setting\Application\Actions\CreateThumbsUseCase;
use App\Modules\Setting\Application\Actions\RecacheUseCase;
use App\Modules\Setting\Application\Actions\ReCreateThumbsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingCacheController extends Controller
{
    public function __construct(
        private readonly ClearAllCacheUseCase  $clearAllCacheUseCase,
        private readonly RecacheUseCase        $recacheUseCase,
        private readonly CreateThumbsUseCase   $createThumbsUseCase,
        private readonly ReCreateThumbsUseCase $reCreateThumbsUseCase,
    )
    {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Setting/Cache/Index', [
            ]
        );
    }

    public function clearAll(): JsonResponse
    {
        $message = $this->clearAllCacheUseCase->execute();
        return response()->json(['message' => $message]);
    }


    public function createThumbs(): JsonResponse
    {
        $message = $this->createThumbsUseCase->execute();
        return response()->json(['message' => $message]);
    }

    public function reCreateThumbs(): JsonResponse
    {
        $message = $this->reCreateThumbsUseCase->execute();
        return response()->json(['message' => $message]);
    }


    public function recache(): JsonResponse
    {
        $message = $this->recacheUseCase->execute();
        return response()->json(['message' => $message]);
    }
}
