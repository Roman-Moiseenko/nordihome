<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Application\Actions\Stock\AddProductToStockUseCase;
use App\Modules\Accounting\Application\Actions\Stock\IndexStockQuery;
use App\Modules\Accounting\Application\DTOs\Stock\FilterStockIndexData;
use App\Modules\Accounting\Application\DTOs\Stock\StockCreateData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function __construct(
        private readonly IndexStockQuery          $indexStockUseCase,
        private readonly AddProductToStockUseCase $addProductToStockUseCase,
    ) {
    }

    public function index(Request $request, UserPermission $permissions): Response
    {
        $filterDto = FilterStockIndexData::validateAndCreate($request->all());
        $products = $this->indexStockUseCase->execute($filterDto, $permissions);

        return Inertia::render('Accounting/Stock/Index', [
            'products' => $products,
            'filters' => $filterDto,
        ]);
    }

    public function addProduct(Request $request, UserPermission $permissions)
    {
        $dto = StockCreateData::validateAndCreate($request->all());
        $this->addProductToStockUseCase->execute($dto);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function addProducts(Request $request, UserPermission $permissions)
    {
        $items = $request->input('products');
        foreach ($items as $item) {
            $dto  = StockCreateData::validateAndCreate($item);
            $this->addProductToStockUseCase->execute($dto);

        }
        return redirect()->back()->with('success', 'Загружено');
    }
}
