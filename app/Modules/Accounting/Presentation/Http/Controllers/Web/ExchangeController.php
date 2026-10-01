<?php

namespace App\Modules\Accounting\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Application\Actions\PriceOutbox\RemovePriceOutboxUseCase;
use App\Modules\Accounting\Application\Actions\PriceOutbox\ResetPriceOutboxProgressUseCase;
use App\Modules\Accounting\Application\DTOs\Exchange\PriceOutAckPayloadData;
use App\Modules\Accounting\Application\DTOs\Exchange\PriceOutPayloadData;
use App\Modules\Accounting\Application\DTOs\Exchange\PricePayloadData;
use App\Modules\Accounting\Application\DTOs\Exchange\StockPayloadData;
use App\Modules\Accounting\Application\Services\Exchange\LoadPriceProductsService;
use App\Modules\Accounting\Application\Services\Exchange\LoadQuantityProductsService;
use App\Modules\Accounting\Application\Services\PriceOutbox\LoadPriceOutboxService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExchangeController extends Controller
{
    public function __construct(
        private readonly LoadPriceProductsService        $loadPriceProductsService,
        private readonly LoadQuantityProductsService     $loadQuantityProductsService,
        private readonly LoadPriceOutboxService          $loadPriceOutboxService,
        private readonly RemovePriceOutboxUseCase        $removePriceOutboxUseCase,
        private readonly ResetPriceOutboxProgressUseCase $resetPriceOutboxProgressUseCase,
    )
    {
    }

    public function stock(Request $request)
    {
        try {
            $dto = StockPayloadData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        if (!$this->loadQuantityProductsService->execute($dto))
            return response()->json(false);

        return response()->json(['ok']);
    }

    public function price(Request $request)
    {
        try {
            $dto = PricePayloadData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        if (!$this->loadPriceProductsService->execute($dto))
            return response()->json(false);

        return response()->json(['ok']);
    }

    public function priceOut(Request $request)
    {
        try {
            PriceOutPayloadData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        return response()->json($this->loadPriceOutboxService->execute());
    }

    public function priceAck(Request $request)
    {
        try {
            $dto = PriceOutAckPayloadData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        if ($dto->succeed) {
            $this->removePriceOutboxUseCase->execute();
        } else {
            $this->resetPriceOutboxProgressUseCase->execute();
        }

        return response()->json(['ok']);
    }
}
