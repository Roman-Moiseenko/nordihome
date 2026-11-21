<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Application\Actions\Organization\CreateForeignOrganizationUseCase;
use App\Modules\Accounting\Application\Actions\Organization\DeleteOrganizationContactUseCase;
use App\Modules\Accounting\Application\Actions\Organization\FindOrganizationByInnUseCase;
use App\Modules\Accounting\Application\Actions\Organization\IndexOrganizationQuery;
use App\Modules\Accounting\Application\Actions\Organization\ListVatQuery;
use App\Modules\Accounting\Application\Actions\Organization\RefreshOrganizationUseCase;
use App\Modules\Accounting\Application\Actions\Organization\RemoveOrganizationUseCase;
use App\Modules\Accounting\Application\Actions\Organization\SearchOrganizationQuery;
use App\Modules\Accounting\Application\Actions\Organization\SyncOrganizationContactUseCase;
use App\Modules\Accounting\Application\Actions\Organization\UpdateOrganizationUseCase;
use App\Modules\Accounting\Application\Actions\Organization\ListOrganizationsQuery;
use App\Modules\Accounting\Application\Actions\Organization\UploadOrganizationFileUseCase;
use App\Modules\Accounting\Application\Actions\Organization\ViewOrganizationQuery;
use App\Modules\Accounting\Application\DTOs\Organization\FilterOrganizationIndexData;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationContactData;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationCreateData;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationUpdateData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function __construct(
        private readonly IndexOrganizationQuery $indexOrganizationQuery,
        private readonly ViewOrganizationQuery $viewOrganizationQuery,
        private readonly FindOrganizationByInnUseCase $findOrganizationByInnUseCase,
        private readonly CreateForeignOrganizationUseCase $createForeignOrganizationUseCase,
        private readonly UpdateOrganizationUseCase $updateOrganizationUseCase,
        private readonly RefreshOrganizationUseCase $refreshOrganizationUseCase,
        private readonly RemoveOrganizationUseCase $removeOrganizationUseCase,
        private readonly SyncOrganizationContactUseCase $syncOrganizationContactUseCase,
        private readonly DeleteOrganizationContactUseCase $deleteOrganizationContactUseCase,
        private readonly UploadOrganizationFileUseCase $uploadOrganizationFileUseCase,
        private readonly SearchOrganizationQuery $searchOrganizationQuery,
        private readonly ListVatQuery $listVatQuery,
        private readonly ListOrganizationsQuery $listOrganizationsQuery,
    ) {
    }

    public function index(Request $request, UserPermission $permission): Response
    {
        $filters = FilterOrganizationIndexData::validateAndCreate($request->all());
        $organizations = $this->indexOrganizationQuery->execute($filters, $permission);

        return Inertia::render('Accounting/Organization/Index', [
            'organizations' => $organizations,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = OrganizationCreateData::validateAndCreate($request->all());
        $organization = $this->findOrganizationByInnUseCase->execute($dto, $permission);

        return redirect()->route('admin.accounting.organization.show', $organization->id)
            ->with('success', 'Организация добавлена');
    }

    public function show(int $id, UserPermission $permission): Response
    {
        $organization = $this->viewOrganizationQuery->execute($id, $permission);

        return Inertia::render('Accounting/Organization/Show', [
            'organization' => $organization,
            'vat' => $this->listVatQuery->execute(),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = OrganizationUpdateData::validateAndCreate($request->all());
        $organization = $this->updateOrganizationUseCase->execute($id, $dto, $permission);

        return redirect()->route('admin.accounting.organization.show', $organization->id)
            ->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $permission): RedirectResponse
    {
        $this->removeOrganizationUseCase->execute($id, $permission);

        return redirect()->route('admin.accounting.organization.index')
            ->with('success', 'Контрагент удалён');
    }

    public function refresh(int $id, UserPermission $permission): RedirectResponse
    {
        $organization = $this->refreshOrganizationUseCase->execute($id, $permission);

        return redirect()->route('admin.accounting.organization.show', $organization->id)
            ->with('success', 'Данные обновлены');
    }

    public function contacts(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = OrganizationContactData::validateAndCreate($request->all());
        $this->syncOrganizationContactUseCase->execute($id, $dto, $permission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function contactDestroy(int $id, int $contactId, UserPermission $permission): RedirectResponse
    {
        $this->deleteOrganizationContactUseCase->execute($contactId, $permission);

        return redirect()->back()->with('success', 'Удалено');
    }

    public function upload(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $this->uploadOrganizationFileUseCase->execute(
            $id,
            $request->file('file'),
            $request->string('type')->value(),
            $request->string('title')->value() ?? '',
            $permission,
        );

        return redirect()->back()->with('success', 'Файл загружен');
    }

    public function find(Request $request, UserPermission $permission): JsonResponse
    {
        $dto = OrganizationCreateData::validateAndCreate($request->all());

        try {
            $organization = $dto->foreign
                ? $this->createForeignOrganizationUseCase->execute($dto, $permission)
                : $this->findOrganizationByInnUseCase->execute($dto, $permission);

            return response()->json($organization->id);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function search(Request $request, UserPermission $permission): JsonResponse
    {
        $result = $this->searchOrganizationQuery->execute(
            $request->string('search')->trim()->value(),
            $permission
        );

        return response()->json($result);
    }

    public function list(): JsonResponse
    {
        return response()->json($this->listOrganizationsQuery->execute());
    }
}
