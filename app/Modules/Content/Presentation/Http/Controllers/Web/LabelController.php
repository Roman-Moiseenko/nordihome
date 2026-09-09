<?php

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\Label\CreateLabelUseCase;
use App\Modules\Content\Application\Actions\Label\IndexLabelUseCase;
use App\Modules\Content\Application\Actions\Label\ListLabelUseCase;
use App\Modules\Content\Application\Actions\Label\RemoveLabelUseCase;
use App\Modules\Content\Application\Actions\Label\UpdateLabelUseCase;
use App\Modules\Content\Application\DTOs\Label\LabelCreateData;
use App\Modules\Content\Application\DTOs\Label\LabelUpdateData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LabelController extends Controller
{
    public function __construct(
        private readonly IndexLabelUseCase $indexLabelUseCase,
        private readonly CreateLabelUseCase $createLabelUseCase,
        private readonly UpdateLabelUseCase $updateLabelUseCase,
        private readonly RemoveLabelUseCase $removeLabelUseCase,
        private readonly ListLabelUseCase $listLabelUseCase,
    ) {}

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $labels = $this->indexLabelUseCase->execute($userPermission);

        return Inertia::render('Content/Label/Index', [
            'labels' => $labels,
        ]);
    }


    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = LabelCreateData::validateAndCreate($request->all());
        $this->createLabelUseCase->execute($dto, $userPermission);

        return redirect()->back()->with('success', 'Метка создана');
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = LabelUpdateData::validateAndCreate($request->all());
        $this->updateLabelUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Метка сохранена');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeLabelUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Метка удалена');
    }

    public function list(): JsonResponse
    {
        $list = $this->listLabelUseCase->execute();

        return response()->json($list);
    }
}
