<?php

declare(strict_types=1);

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Setting\Application\Actions\ClearAllCacheUseCase;
use App\Modules\Setting\Application\Actions\ClearImageCacheUseCase;
use App\Modules\Setting\Application\Actions\RecacheUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingCacheController extends Controller
{
    public function __construct(
        private readonly ClearAllCacheUseCase $clearAllCacheUseCase,
        private readonly ClearImageCacheUseCase $clearImageCacheUseCase,
        private readonly RecacheUseCase $recacheUseCase,
    ) {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Setting/Cache/Index', [
            ]
        );
    }

    public function clearAll(): JsonResponse
    {
        try {
            $message = $this->clearAllCacheUseCase->execute();
            return response()->json(['message' => $message]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function clearImage(): JsonResponse
    {
        try {
            $message = $this->clearImageCacheUseCase->execute();
            return response()->json(['message' => $message]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function recache(): JsonResponse
    {
        try {
            $message = $this->recacheUseCase->execute();
            return response()->json(['message' => $message]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
