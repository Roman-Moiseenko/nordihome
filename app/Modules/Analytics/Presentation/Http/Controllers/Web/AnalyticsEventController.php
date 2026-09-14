<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Application\Actions\Action\TrackActionUseCase;
use App\Modules\Analytics\Application\Actions\Exit\RecordExitUseCase;
use App\Modules\Analytics\Application\DTOs\Action\TrackActionData;
use App\Modules\Analytics\Application\DTOs\Exit\RecordExitData;
use App\Modules\Analytics\Application\DTOs\Search\TrackSearchClickData;
use App\Modules\Analytics\Application\Services\TrackSearchClickService;
use App\Modules\Analytics\Infrastructure\Services\VisitorUuidGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AnalyticsEventController — публичные endpoint'ы для событий из JS.
 *
 * Без аутентификации (идентификация по cookie user_cookie_id), исключены из
 * VerifyCsrfToken. Вся проверка/валидация выполняется в UseCase и DTO.
 */
class AnalyticsEventController extends Controller
{
    public function __construct(
        private readonly TrackActionUseCase      $trackAction,
        private readonly RecordExitUseCase       $recordExit,
        private readonly TrackSearchClickService $trackSearchClick,
    ) {}

    public function trackAction(Request $request): JsonResponse
    {
    //    \Log::info(json_encode($request->all()));
        $dto = TrackActionData::validateAndCreate($request->all());
     //   \Log::info('!');
        $ok = $this->trackAction->execute(
            $dto,
            $request->cookie(VisitorUuidGenerator::COOKIE_NAME),
        );

        return response()->json(['ok' => $ok]);
    }

    public function recordExit(Request $request): JsonResponse
    {

        $dto = RecordExitData::validateAndCreate($request->all());
        $ok = $this->recordExit->execute(
            $dto,
            $request->cookie(VisitorUuidGenerator::COOKIE_NAME),
        );

        return response()->json(['ok' => $ok]);
    }

    public function trackSearchClick(Request $request): JsonResponse
    {
        $dto = TrackSearchClickData::validateAndCreate($request->all());
        $ok = $this->trackSearchClick->execute(
            $dto,
            $request->cookie(VisitorUuidGenerator::COOKIE_NAME),
        );

        return response()->json(['ok' => $ok]);
    }
}
