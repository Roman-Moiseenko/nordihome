<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\AttributeGroup\CreateAttributeGroupUseCase;
use App\Modules\Catalog\Application\Actions\AttributeGroup\IndexAttributeGroupQuery;
use App\Modules\Catalog\Application\Actions\AttributeGroup\ListAttributeGroupAttributesQuery;
use App\Modules\Catalog\Application\Actions\AttributeGroup\RemoveAttributeGroupUseCase;
use App\Modules\Catalog\Application\Actions\AttributeGroup\SortAttributeGroupUseCase;
use App\Modules\Catalog\Application\Actions\AttributeGroup\UpdateAttributeGroupUseCase;
use App\Modules\Catalog\Application\Actions\AttributeGroup\ViewAttributeGroupQuery;
use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupCreateData;
use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupSortData;
use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupUpdateData;
use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupViewData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttributeGroupController extends Controller
{
    public function __construct(
        private readonly IndexAttributeGroupQuery $indexAttributeGroupQuery,
        private readonly CreateAttributeGroupUseCase $createAttributeGroupUseCase,
        private readonly ViewAttributeGroupQuery $viewAttributeGroupQuery,
        private readonly ListAttributeGroupAttributesQuery $listAttributeGroupAttributesQuery,
        private readonly UpdateAttributeGroupUseCase $updateAttributeGroupUseCase,
        private readonly RemoveAttributeGroupUseCase $removeAttributeGroupUseCase,
        private readonly SortAttributeGroupUseCase $sortAttributeGroupUseCase,
    )
    {
    }

    public function index(UserPermission $userPermission): Response
    {
        $groups = $this->indexAttributeGroupQuery->execute($userPermission);

        return Inertia::render('Catalog/Attribute/Groups/Index', [
            'groups' => $groups,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AttributeGroupCreateData::validateAndCreate($request->all());
        $group = $this->createAttributeGroupUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.catalog.attribute-group.show', $group->id)
            ->with('success', 'Группа добавлена');
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $group = $this->viewAttributeGroupQuery->execute($id, $userPermission);
        $attributes = $this->listAttributeGroupAttributesQuery->execute($id, $userPermission);

        return Inertia::render('Catalog/Attribute/Groups/Show', [
            'group' => AttributeGroupViewData::fromEntity($group, $attributes),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AttributeGroupUpdateData::validateAndCreate($request->all());
        $group = $this->updateAttributeGroupUseCase->execute($id, $dto, $userPermission);

        return redirect()->route('admin.catalog.attribute-group.show', $group->id)
            ->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        try {
            $this->removeAttributeGroupUseCase->execute($id, $userPermission);
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.catalog.attribute-group.index')
            ->with('success', 'Группа удалена');
    }

    public function sort(Request $request, UserPermission $userPermission): JsonResponse
    {
        $dto = AttributeGroupSortData::validateAndCreate($request->all());
        $this->sortAttributeGroupUseCase->execute($dto, $userPermission);

        return response()->json(['message' => 'Порядок сортировки обновлён']);
    }
}
