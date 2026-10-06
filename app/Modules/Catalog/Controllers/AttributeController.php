<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Attribute\CreateAttributeUseCase;
use App\Modules\Catalog\Application\Actions\Attribute\IndexAttributeQuery;
use App\Modules\Catalog\Application\Actions\Attribute\ListAttributeGroupQuery;
use App\Modules\Catalog\Application\Actions\Attribute\RemoveAttributeUseCase;
use App\Modules\Catalog\Application\Actions\Attribute\UpdateAttributeUseCase;
use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCreateData;
use App\Modules\Catalog\Application\DTOs\Attribute\AttributeUpdateData;
use App\Modules\Catalog\Application\DTOs\Attribute\FilterAttributeIndexData;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Repository\AttributeRepository;
use App\Modules\Catalog\Service\AttributeService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttributeController extends Controller
{

    private AttributeService $service;
    private AttributeRepository $repository;


    public function __construct(
        AttributeService                         $service,
        AttributeRepository                      $repository,
        private readonly ListAttributeGroupQuery $listAttributeGroupQuery,
        private readonly IndexAttributeQuery     $indexAttributeQuery,
        private readonly CreateAttributeUseCase  $createAttributeUseCase,
        private readonly UpdateAttributeUseCase  $updateAttributeUseCase,
        private readonly RemoveAttributeUseCase  $removeAttributeUseCase,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $filters = FilterAttributeIndexData::validateAndCreate($request->all());
        $attributes = $this->indexAttributeQuery->execute($filters, $userPermission);
        return Inertia::render('Catalog/Attribute/Index', [
            'attributes' => $attributes,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AttributeCreateData::validateAndCreate($request->all());

        $attribute = $this->createAttributeUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.catalog.attribute.show', $attribute->id)->with('success', 'Атрибут создан');

    }

    public function show(int $id): Response
    {
        return Inertia::render('Catalog/Attribute/Show', [
            'attribute' => $this->repository->AttributeWithToArray(Attribute::findOrFail($id)),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AttributeUpdateData::validateAndCreate($request->all());

        $this->updateAttributeUseCase->execute($id, $dto, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');

    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeAttributeUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Атрибут удален');
    }

    /*
    //Варианты
    public function variant_image(Request $request, AttributeVariant $variant)
    {
        $this->service->image_variant($variant, $request);
        return redirect()->back();
    }*/
    public function types(): JsonResponse
    {
        $list = $this->listAttributeGroupQuery->execute();
        return \response()->json($list);
    }
}
