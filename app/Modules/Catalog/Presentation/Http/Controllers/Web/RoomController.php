<?php

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Modules\Catalog\Application\Actions\Room\CreateRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\DownRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\IndexRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\RemoveRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\ToggleRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\TreeRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\UpRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\UpdateRoomUseCase;
use App\Modules\Catalog\Application\Actions\Room\ViewRoomUseCase;
use App\Modules\Catalog\Application\DTOs\Room\RoomCreateData;
use App\Modules\Catalog\Application\DTOs\Room\RoomIndexData;
use App\Modules\Catalog\Application\DTOs\Room\RoomTreeData;
use App\Modules\Catalog\Application\DTOs\Room\RoomUpdateData;
use App\Modules\Catalog\Application\DTOs\Room\RoomViewData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class RoomController
{

    public function __construct(
        public readonly IndexRoomUseCase $indexRoomUseCase,
        public readonly CreateRoomUseCase $createRoomUseCase,
        public readonly ViewRoomUseCase $viewRoomUseCase,
        public readonly UpdateRoomUseCase $updateRoomUseCase,
        public readonly RemoveRoomUseCase $removeRoomUseCase,
        public readonly TreeRoomUseCase $treeRoomUseCase,
        public readonly UpRoomUseCase $upRoomUseCase,
        public readonly DownRoomUseCase $downRoomUseCase,
        public readonly ToggleRoomUseCase $toggleRoomUseCase,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission)
    {
        $rooms = $this->indexRoomUseCase->execute($userPermission);
        return Inertia::render('Catalog/Room/Index', [
            'rooms' => RoomIndexData::collect($rooms),
        ]);
    }

    public function store(Request $request, UserPermission $userPermission)
    {
        $dto = RoomCreateData::validateAndCreate($request->all());

        $roomDTO = $this->createRoomUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.catalog.room.show', $roomDTO->id);
    }

    public function show(int $id, UserPermission $userPermission)
    {
        $room = $this->viewRoomUseCase->execute($id, $userPermission);
        return Inertia::render('Catalog/Room/Show', [
           'room' => RoomViewData::fromEntity($room),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission)
    {
        $dto = RoomUpdateData::validateAndCreate($request->all());

        $roomDto = $this->updateRoomUseCase->execute($id, $dto, $userPermission);
        return redirect()->route('admin.catalog.room.show', $roomDto->id)->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission)
    {
        $this->removeRoomUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Комната удалена');
    }

    public function up(int $id, UserPermission $userPermission)
    {
        $this->upRoomUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down(int $id, UserPermission $userPermission)
    {
        $this->downRoomUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function toggle(int $id, UserPermission $userPermission)
    {
        $this->toggleRoomUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    /*  api запросы справочники */
    public function tree()
    {
        $rooms = $this->treeRoomUseCase->execute();
        return response()->json(RoomTreeData::fromEntityArray($rooms), Response::HTTP_OK);

    }
}
