<?php

namespace App\Modules\Parser\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Application\Actions\Product\IndexParserProductUseCase;
use App\Modules\Parser\Application\Actions\Product\ToggleProductAvailabilityUseCase;
use App\Modules\Parser\Application\Actions\Product\ToggleProductFragileUseCase;
use App\Modules\Parser\Application\Actions\Product\ToggleProductSanctionedUseCase;
use App\Modules\Parser\Application\DTOs\Product\ParserProductFilterData;
use App\Modules\Parser\Infrastructure\Jobs\UpdateProductIkeaJob;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductParserController extends Controller
{

    public function __construct(
        private readonly IndexParserProductUseCase $indexParserProductUseCase,
        private readonly ToggleProductAvailabilityUseCase $toggleProductAvailabilityUseCase,
        private readonly ToggleProductFragileUseCase $toggleProductFragileUseCase,
        private readonly ToggleProductSanctionedUseCase $toggleProductSanctionedUseCase,
    ) {}

    public function index(Request $request, UserPermission $userPermission): \Inertia\Response
    {
        $dto = ParserProductFilterData::validateAndCreate($request->all());
        $products = $this->indexParserProductUseCase->execute($dto, $userPermission);
        return Inertia::render('Parser/Product/Index', [
            'products' => $products,
            'filters' => $dto,

        ]);
    }

    public function show(int $id)
    {
        return Inertia::render('Parser/Product/Show', [
          //  'products' => $this->repository->ProductWithToArray($product_parser),
        ]);
    }

    public function available(int $id, UserPermission $userPermission): RedirectResponse
    {
        $message = $this->toggleProductAvailabilityUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', $message);
    }

    public function fragile(int $id, UserPermission $userPermission): RedirectResponse
    {
        $message = $this->toggleProductFragileUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', $message);
    }

    public function sanctioned(int $id, UserPermission $userPermission): RedirectResponse
    {
        $message = $this->toggleProductSanctionedUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', $message);
    }

    public function parser(int $id)
    {
        UpdateProductIkeaJob::dispatch($id);
        return response()->json(['message' => 'Товар в очереди на спарсивание']);
    }


}
