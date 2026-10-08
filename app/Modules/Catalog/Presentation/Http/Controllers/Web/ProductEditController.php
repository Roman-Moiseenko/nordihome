<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Modification\GetModificationByProductQuery;
use App\Modules\Catalog\Application\Actions\Modification\GetProductModificationAttributesQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetAttributeProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetBonusProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetCommonProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetCompositeProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetDescriptionProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetDimensionsProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetEquivalentProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetManagementProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetRelatedProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\GetVideoProductQuery;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetAttributeProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetBonusProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetCommonProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetCompositeProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetDescriptionProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetDimensionsProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetEquivalentProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetManagementProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetRelatedProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\EditPage\SetVideoProductUseCase;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateAttributeProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateBonusProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateCommonProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateCompositeProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateDescriptionProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateDimensionsProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateEquivalentProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateManagementProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateRelatedProductData;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateVideoProductData;
use Illuminate\Http\Request;
use function Laravel\Prompts\warning;

class ProductEditController extends Controller
{
    public function __construct(
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
        private readonly GetManagementProductQuery $getManagementProductQuery,
        private readonly SetManagementProductUseCase $setManagementProductUseCase,
        private readonly GetModificationByProductQuery $getModificationByProductQuery,
        private readonly GetProductModificationAttributesQuery $getProductModificationAttributesQuery,
        private readonly GetEquivalentProductQuery $getEquivalentProductQuery,
        private readonly SetEquivalentProductUseCase $setEquivalentProductUseCase,
        private readonly GetRelatedProductQuery $getRelatedProductQuery,
        private readonly SetRelatedProductUseCase $setRelatedProductUseCase,
        private readonly GetBonusProductQuery $getBonusProductQuery,
        private readonly SetBonusProductUseCase $setBonusProductUseCase,
        private readonly GetCompositeProductQuery $getCompositeProductQuery,
        private readonly SetCompositeProductUseCase $setCompositeProductUseCase,
    ) {
    }

    public function loadCommon(int $id)
    {
        return response()->json($this->getCommonProductQuery->execute($id));
    }

    public function saveCommon(Request $request, int $id)
    {
        $dto = UpdateCommonProductData::validateAndCreate($request->all());
        $this->setCommonProductUseCase->execute($id, $dto);
        return response()->json($this->getCommonProductQuery->execute($id));
    }

    public function loadDescription(int $id)
    {
        return response()->json($this->getDescriptionProductQuery->execute($id));
    }

    public function saveDescription(Request $request, int $id)
    {
        $dto = UpdateDescriptionProductData::validateAndCreate($request->all());
        $this->setDescriptionProductUseCase->execute($id, $dto);
        return response()->json($this->getDescriptionProductQuery->execute($id));
    }

    public function loadDimensions(int $id)
    {
        return response()->json($this->getDimensionsProductQuery->execute($id));
    }

    public function saveDimensions(Request $request, int $id)
    {
        $dto = UpdateDimensionsProductData::validateAndCreate($request->all());
        $this->setDimensionsProductUseCase->execute($id, $dto);
        return response()->json($this->getDimensionsProductQuery->execute($id));
    }

    public function loadVideo(int $id)
    {
        return response()->json($this->getVideoProductQuery->execute($id));
    }

    public function saveVideo(Request $request, int $id)
    {
        $dto = UpdateVideoProductData::validateAndCreate($request->all());
        $this->setVideoProductUseCase->execute($id, $dto);
        return response()->json($this->getVideoProductQuery->execute($id));
    }

    public function loadAttribute(int $id)
    {
        return response()->json($this->getAttributeProductQuery->execute($id));
    }

    public function saveAttribute(Request $request, int $id)
    {
        $dto = UpdateAttributeProductData::validateAndCreate($request->all());
        $this->setAttributeProductUseCase->execute($id, $dto);
        return response()->json($this->getAttributeProductQuery->execute($id));
    }

    public function loadManagement(int $id)
    {
        return response()->json($this->getManagementProductQuery->execute($id));
    }

    public function saveManagement(Request $request, int $id)
    {
        $dto = UpdateManagementProductData::validateAndCreate($request->all());
        $this->setManagementProductUseCase->execute($id, $dto);
        return response()->json($this->getManagementProductQuery->execute($id));
    }

    public function loadModification(int $id)
    {
        $data = $this->getModificationByProductQuery->execute($id);
        return response()->json($data);
    }

    public function loadModificationAttributes(int $id)
    {
        return response()->json($this->getProductModificationAttributesQuery->execute($id));
    }

    public function loadEquivalent(int $id)
    {
        return response()->json($this->getEquivalentProductQuery->execute($id));
    }

    public function saveEquivalent(Request $request, int $id)
    {
        $dto = UpdateEquivalentProductData::validateAndCreate($request->all());
        $this->setEquivalentProductUseCase->execute($id, $dto);
        return response()->json($this->getEquivalentProductQuery->execute($id));
    }

    public function loadRelated(int $id)
    {
        return response()->json($this->getRelatedProductQuery->execute($id));
    }

    public function saveRelated(Request $request, int $id)
    {
        $dto = UpdateRelatedProductData::validateAndCreate($request->all());
        $this->setRelatedProductUseCase->execute($id, $dto);
        return response()->json($this->getRelatedProductQuery->execute($id));
    }

    public function loadBonus(int $id)
    {
        return response()->json($this->getBonusProductQuery->execute($id));
    }

    public function saveBonus(Request $request, int $id)
    {
        $dto = UpdateBonusProductData::validateAndCreate($request->all());
        $this->setBonusProductUseCase->execute($id, $dto);
        return response()->json($this->getBonusProductQuery->execute($id));
    }

    public function loadComposite(int $id)
    {
        return response()->json($this->getCompositeProductQuery->execute($id));
    }

    public function saveComposite(Request $request, int $id)
    {
        $dto = UpdateCompositeProductData::validateAndCreate($request->all());
        $this->setCompositeProductUseCase->execute($id, $dto);
        return response()->json($this->getCompositeProductQuery->execute($id));
    }
}
