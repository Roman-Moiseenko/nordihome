<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Packages;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetCommonProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetCommonProductUseCase;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateCommonProductData;
use App\Modules\Catalog\Infrastructure\Models\AttributeGroup;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use App\Modules\Catalog\Infrastructure\Models\Equivalent;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Series;
use App\Modules\Catalog\Application\Actions\Product\SearchProductQuery;
use App\Modules\Catalog\Infrastructure\Models\Tag;
use App\Modules\Catalog\Repository\ProductRepository;
use App\Modules\Catalog\Request\ProductCreateRequest;
use App\Modules\Catalog\Service\ProductService;
use App\Modules\Content\Application\Services\ProductSearchService;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Entity\VAT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Log;

class ProductEditController extends Controller
{
    private ProductService $service;

    public function __construct(
        ProductService $service,
        private readonly GetCommonProductQuery $getCommonProductQuery,
        private readonly SetCommonProductUseCase $setCommonProductUseCase,
    )
    {
        $this->service = $service;
    }

    //Vue3 Edit
    public function loadCommon(int $id)
    {
        $data = $this->getCommonProductQuery->execute($id);
        return \response()->json($data);
    }
    public function saveCommon(ProductCreateRequest $request, int $id)
    {
        $dto = UpdateCommonProductData::validateAndCreate($request->all());
        $this->setCommonProductUseCase->execute($id, $dto);
        $data = $this->getCommonProductQuery->execute($id);
        return \response()->json($data);

    }

    public function description(Request $request, Product $product): RedirectResponse
    {
        $this->service->editDescription($product, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function dimensions(Request $request, Product $product): RedirectResponse
    {
        $this->service->editDimensions($product, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function video(Request $request, Product $product): RedirectResponse
    {
        $this->service->editVideo($product, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function attribute(Request $request, Product $product): RedirectResponse
    {
        $this->service->editAttribute($product, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function management(Request $request, Product $product): RedirectResponse
    {

        $this->service->editManagement($product, $request);
        return redirect()->back()->with('success', 'Сохранено');

    }

    public function equivalent(Request $request, Product $product): RedirectResponse
    {

        $this->service->editEquivalent($product, $request);
        return redirect()->back()->with('success', 'Сохранено');

    }

    public function related(Request $request, Product $product): RedirectResponse
    {

        $this->service->editRelated($product, $request);
        return redirect()->back()->with('success', 'Сохранено');

    }

    public function bonus(Request $request, Product $product): RedirectResponse
    {

        $this->service->editBonus($product, $request);
        return redirect()->back()->with('success', 'Сохранено');

    }

    public function composite(Request $request, Product $product): RedirectResponse
    {

        $this->service->editComposite($product, $request);
        return redirect()->back()->with('success', 'Сохранено');

    }



}
