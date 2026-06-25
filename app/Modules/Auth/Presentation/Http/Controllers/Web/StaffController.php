<?php

namespace App\Modules\Auth\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Auth\Application\Actions\Staff\CreateStaffUseCase;
use App\Modules\Auth\Application\Actions\Staff\IndexStaffUseCase;
use App\Modules\Auth\Application\Actions\Staff\ListStaffGroupPositions;
use App\Modules\Auth\Application\Actions\Staff\RemoveStaffUseCase;
use App\Modules\Auth\Application\Actions\Staff\UpdateStaffUseCase;
use App\Modules\Auth\Application\Actions\Staff\ViewStaffUseCase;
use App\Modules\Auth\Application\Actions\User\RegisterStaffUserUseCase;
use App\Modules\Auth\Application\Actions\User\UpdateUserUseCase;
use App\Modules\Auth\Application\DTOs\Staff\StaffCreateData;
use App\Modules\Auth\Application\DTOs\Staff\StaffIndexData;
use App\Modules\Auth\Application\DTOs\Staff\StaffUpdateData;
use App\Modules\Auth\Application\DTOs\Staff\StaffViewData;
use App\Modules\Auth\Application\DTOs\User\UpdateUserData;
use App\Modules\Auth\Application\DTOs\User\UserViewData;
use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class StaffController extends Controller
{
    public function __construct(
        private readonly StaffRepositoryInterface $staffRepository,
        private readonly CreateStaffUseCase       $createStaffUseCase,
        private readonly UpdateStaffUseCase       $updateStaffUseCase,
        private readonly RegisterStaffUserUseCase $registerStaffUserUseCase,
        private readonly UpdateUserUseCase        $updateUserUseCase,
        private readonly RemoveStaffUseCase       $removeStaffUseCase,
        private readonly IndexStaffUseCase        $indexStaffUseCase,
        private readonly ViewStaffUseCase         $viewStaffUseCase,
        private readonly ListStaffGroupPositions  $listStaffGroupPositions,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission): \Inertia\Response
    {
        //TODO Сделать filters
        $staffs = $this->indexStaffUseCase->execute($userPermission);
        return Inertia::render('Auth/Staff/Index', [
            'staffs' => StaffIndexData::collect($staffs),
            'filters' => [],
        ]);

        //return response()->json(StaffIndexData::collect($staffs), Response::HTTP_CREATED);
    }

    public function show(int $id, UserPermission $userPermission): \Inertia\Response
    {
        $staff = $this->viewStaffUseCase->execute($id, $userPermission);

        return Inertia::render('Auth/Staff/Show', [
            'staff' => StaffViewData::fromEntity($staff),
        ]);

        // return response()->json(StaffViewData::fromEntity($staff), Response::HTTP_CREATED);
    }

    /**
     * @throws \Throwable
     */
    public function store(Request $request, UserPermission $userPermission)
    {
        try {
            $dto = StaffCreateData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $staffDTO = $this->createStaffUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.staff.show', $staffDTO->id);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function update(Request $request, int $id, UserPermission $userPermission)
    {

        $dto = StaffUpdateData::validateAndCreate($request->all());

        $staff = $this->updateStaffUseCase->execute($id, $dto, $userPermission);
        return redirect()->route('admin.staff.show', $staff->id);
    }

    public function destroy(int $id, UserPermission $userPermission): JsonResponse
    {
        $staff = $this->staffRepository->findById($id);
        if (!$staff) return response()->json(['message' => 'Сотрудник не найден'], Response::HTTP_NOT_FOUND);

        $deleted = $this->removeStaffUseCase->execute($id, $userPermission);
        if (!$deleted)
            return response()->json(['message' => 'Ошибка удаления сотрудника'], Response::HTTP_NOT_MODIFIED);

        return response()->json(null, Response::HTTP_OK);
    }

    public function user(Request $request, int $id, UserPermission $userPermission)
    {
        $staff = $this->staffRepository->findById($id);
        if (!$staff) return response()->json(['message' => 'Сотрудник не найден'], Response::HTTP_NOT_FOUND);

        $dto = UpdateUserData::validateAndCreate($request->all());

        if (is_null($staff->user)) {
            $userOut = $this->registerStaffUserUseCase->execute($id, $dto, $userPermission);
        } else {
            $userOut = $this->updateUserUseCase->execute($id, $dto, $userPermission);
        }
        return redirect()->route('admin.staff.show', $staff->id);
    }

    public function positions()
    {
        return response()->json(array_select(StaffPosition::positions()), Response::HTTP_OK);
    }

    public function groups(Request $request, UserPermission $userPermission)
    {
        $staffs = $this->listStaffGroupPositions->execute();
        return response()->json($staffs, Response::HTTP_OK);
    }

}
