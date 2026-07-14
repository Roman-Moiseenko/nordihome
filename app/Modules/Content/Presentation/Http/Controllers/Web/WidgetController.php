<?php

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\Widget\CreateWidgetUseCase;
use App\Modules\Content\Application\Actions\Widget\IndexWidgetUseCase;
use App\Modules\Content\Application\Actions\Widget\RemoveWidgetUseCase;
use App\Modules\Content\Application\Actions\Widget\UpdateWidgetUseCase;
use App\Modules\Content\Application\Actions\Widget\ViewWidgetUseCase;
use App\Modules\Content\Application\DTOs\Widget\WidgetCreateData;
use App\Modules\Content\Application\DTOs\Widget\WidgetIndexData;
use App\Modules\Content\Application\DTOs\Widget\WidgetUpdateData;
use App\Modules\Content\Application\DTOs\Widget\WidgetViewData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WidgetController extends Controller
{
    public function __construct(
        private readonly CreateWidgetUseCase $createWidgetUseCase,
        private readonly UpdateWidgetUseCase $updateWidgetUseCase,
        private readonly RemoveWidgetUseCase $removeWidgetUseCase,
        private readonly IndexWidgetUseCase  $indexWidgetUseCase,
        private readonly ViewWidgetUseCase   $viewWidgetUseCase,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission)
    {
        $widgets = $this->indexWidgetUseCase->execute($userPermission);
        return Inertia::render('Content/Widget/Index', [
            'widgets' => WidgetIndexData::collect($widgets),
        ]);
    }

    public function store(Request $request, UserPermission $userPermission)
    {
        $dto = WidgetCreateData::validateAndCreate($request->all());

        $widgetDTO = $this->createWidgetUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.content.widget.show', $widgetDTO->id);
    }

    public function show(int $id, UserPermission $userPermission)
    {
        $widget = $this->viewWidgetUseCase->execute($id, $userPermission);

        return Inertia::render('Content/Widget/Show', [
            'widget' => WidgetViewData::fromEntity($widget),
        ]);
    }

    public function update(Request $request, int $id, UserPermission $userPermission)
    {
        $dto = WidgetUpdateData::validateAndCreate($request->all());

        $widgetDTO = $this->updateWidgetUseCase->execute($id, $dto, $userPermission);
        return redirect()->route('admin.content.widget.show', $widgetDTO->id);

    }


    public function destroy(int $id, UserPermission $userPermission)
    {
        $this->removeWidgetUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Виджет удален');
    }
}
