<?php

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Modules\Catalog\Application\Actions\CreateRoomUseCase;
use App\Modules\Catalog\Application\Actions\IndexRoomUseCase;
use App\Modules\Catalog\Application\Actions\RemoveRoomUseCase;
use App\Modules\Catalog\Application\Actions\TreeRoomUseCase;
use App\Modules\Catalog\Application\Actions\UpdateRoomUseCase;
use App\Modules\Catalog\Application\Actions\ViewRoomUseCase;
use App\Modules\Catalog\Application\DTOs\RoomCreateData;
use App\Modules\Catalog\Application\DTOs\RoomIndexData;
use App\Modules\Catalog\Application\DTOs\RoomTreeData;
use App\Modules\Catalog\Application\DTOs\RoomViewData;
use App\Modules\Catalog\Application\DTOs\UpdateRoomData;
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
        $dto = UpdateRoomData::validateAndCreate($request->all());

        $roomDto = $this->updateRoomUseCase->execute($id, $dto, $userPermission);
        return redirect()->route('admin.catalog.room.show', $roomDto->id)->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission)
    {
        $this->removeRoomUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Комната удалена');
    }

    /*  api запросы справочники */

    public function tree()
    {
        $rooms = $this->treeRoomUseCase->execute();
        return response()->json(RoomTreeData::collect($rooms), Response::HTTP_OK);

    }
}
