<?php

namespace App\Modules\Auth\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Actions\Role\CreateCustomRoleUseCase;
use App\Modules\Auth\Application\Actions\Role\DeleteCustomRoleUseCase;
use App\Modules\Auth\Application\Actions\Role\IndexCustomRoleUseCase;
use App\Modules\Auth\Application\Actions\Role\UpdateCustomRoleUseCase;
use App\Modules\Auth\Application\Actions\Role\ViewCustomRoleUseCase;
use App\Modules\Auth\Application\DTOs\Role\RoleCreateData;
use App\Modules\Auth\Application\DTOs\Role\RoleUpdateData;
use App\Modules\Auth\Application\DTOs\Role\RoleViewData;
use App\Modules\Auth\Domain\Services\PermissionProviderInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    public function __construct(
        private readonly CreateCustomRoleUseCase $createRole,
        private readonly UpdateCustomRoleUseCase $updateRole,
        private readonly DeleteCustomRoleUseCase $deleteRole,
        private readonly PermissionProviderInterface $permissionProvider,
        private readonly ViewCustomRoleUseCase  $viewCustomRoleUseCase,
        private readonly IndexCustomRoleUseCase $indexCustomRoleUseCase
    ) {}

    public function index(Request $request, UserPermission $userPermission): \Inertia\Response
    {
        $is_system = $request->has('type') && $request->type == 'system';
        $roles = $this->indexCustomRoleUseCase->execute($is_system, $userPermission);

        return Inertia::render('Auth/Role/Index', [
            'roles' => RoleViewData::collect($roles),
            'filters' => [
                'type' => $request->type ?? 'custom',
            ],
        ]);
    }

    public function show(int $id, UserPermission $userPermission): \Inertia\Response
    {
        $role = $this->viewCustomRoleUseCase->execute($id, $userPermission);

        return Inertia::render('Auth/Role/Show', [
            'role' => RoleViewData::fromEntity($role),
        ]);
    }

    public function store(Request $request, UserPermission $userPermission)
    {
        try {
            $dto = RoleCreateData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $role = $this->createRole->execute($dto, $userPermission);
        return redirect()->route('admin.role.show', $role->id);
    }

    public function update(int $id, Request $request, UserPermission $userPermission)
    {
        try {
            $dto = RoleUpdateData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $updatedRole = $this->updateRole->execute($id, $dto, $userPermission);
        return redirect()->route('admin.role.show', $updatedRole->id);
    }

    public function destroy(int $id, UserPermission $userPermission): JsonResponse
    {
        try {
            $this->deleteRole->execute($id, $userPermission);
            return response()->json(null, Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function roles(Request $request, UserPermission $userPermission): JsonResponse
    {
        $roles = $this->indexCustomRoleUseCase->execute(false, $userPermission);
        return response()->json(RoleViewData::collect($roles), Response::HTTP_CREATED);
    }

    public function permissions(UserPermission $userPermission): JsonResponse
    {
        return response()->json(
            $this->permissionProvider->groupedBySystemRoles()
        );
    }
}
