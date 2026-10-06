<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Attribute\CreateAttributeUseCase;
use App\Modules\Catalog\Application\Actions\Attribute\IndexAttributeQuery;
use App\Modules\Catalog\Application\Actions\Attribute\ListAttributeGroupQuery;
use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCreateData;
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
        AttributeService         $service,
        AttributeRepository      $repository,
        private readonly ListAttributeGroupQuery $listAttributeGroupQuery,
        private readonly IndexAttributeQuery $indexAttributeQuery,
        private readonly CreateAttributeUseCase $createAttributeUseCase,
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

        try {
            $attribute = $this->createAttributeUseCase->execute($dto, $userPermission);
            return redirect()->route('admin.catalog.attribute.show', $attribute->id)->with('success', 'Атрибут создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Attribute $attribute): Response
    {
        return Inertia::render('Catalog/Attribute/Show', [
            'attribute' => $this->repository->AttributeWithToArray($attribute),
        ]);
    }

    public function set_info(Request $request, Attribute $attribute): RedirectResponse
    {
        try {
            $this->service->setInfo($request, $attribute);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        try {
            $this->service->delete($attribute);
            return redirect()->back()->with('success', 'Атрибут удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
