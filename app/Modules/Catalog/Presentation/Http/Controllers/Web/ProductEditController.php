<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Packages;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetAttributeProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetCommonProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetDescriptionProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetDimensionsProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetVideoProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetAttributeProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetCommonProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetDescriptionProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetDimensionsProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetVideoProductUseCase;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateAttributeProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateCommonProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateDescriptionProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateDimensionsProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateVideoProductData;
use App\Modules\Catalog\Infrastructure\Models\AttributeGroup;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use App\Modules\Catalog\Infrastructure\Models\Equivalent;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Series;
use App\Modules\Catalog\Application\Actions\Product\SearchProductQuery;
use App\Modules\Catalog\Infrastructure\Models\Tag;
use App\Modules\Catalog\Repository\ProductRepository;
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
        private readonly GetDescriptionProductQuery $getDescriptionProductQuery,
        private readonly SetDescriptionProductUseCase $setDescriptionProductUseCase,
        private readonly GetDimensionsProductQuery $getDimensionsProductQuery,
        private readonly SetDimensionsProductUseCase $setDimensionsProductUseCase,
        private readonly GetVideoProductQuery $getVideoProductQuery,
        private readonly SetVideoProductUseCase $setVideoProductUseCase,
        private readonly GetAttributeProductQuery $getAttributeProductQuery,
        private readonly SetAttributeProductUseCase $setAttributeProductUseCase,
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
    public function saveCommon(Request $request, int $id)
    {
        $dto = UpdateCommonProductData::validateAndCreate($request->all());
        $this->setCommonProductUseCase->execute($id, $dto);
        $data = $this->getCommonProductQuery->execute($id);
        return \response()->json($data);
    }

    public function loadDescription(int $id)
    {
        $data = $this->getDescriptionProductQuery->execute($id);
        return \response()->json($data);
    }
    public function saveDescription(Request $request, int $id)
    {
        $dto = UpdateDescriptionProductData::validateAndCreate($request->all());
        $this->setDescriptionProductUseCase->execute($id, $dto);
        $data = $this->getDescriptionProductQuery->execute($id);
        return \response()->json($data);
    }

    public function loadDimensions(int $id)
    {
        $data = $this->getDimensionsProductQuery->execute($id);
        return \response()->json($data);
    }
    public function saveDimensions(Request $request, int $id)
    {
        $dto = UpdateDimensionsProductData::validateAndCreate($request->all());
        $this->setDimensionsProductUseCase->execute($id, $dto);
        $data = $this->getDimensionsProductQuery->execute($id);
        return \response()->json($data);
    }

    public function loadVideo(int $id)
    {
        $data = $this->getVideoProductQuery->execute($id);
        return \response()->json($data);
    }
    public function saveVideo(Request $request, int $id)
    {
        $dto = UpdateVideoProductData::validateAndCreate($request->all());
        $this->setVideoProductUseCase->execute($id, $dto);
        $data = $this->getVideoProductQuery->execute($id);
        return \response()->json($data);
    }

    public function loadAttribute(int $id)
    {
        $data = $this->getAttributeProductQuery->execute($id);
        return \response()->json($data);
    }
    public function saveAttribute(Request $request, int $id)
    {
        $dto = UpdateAttributeProductData::validateAndCreate($request->all());
        $this->setAttributeProductUseCase->execute($id, $dto);
        $data = $this->getAttributeProductQuery->execute($id);
        return \response()->json($data);
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
