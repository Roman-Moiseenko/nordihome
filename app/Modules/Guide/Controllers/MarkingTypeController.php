<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Service\MarkingTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarkingTypeController extends Controller
{
    private MarkingTypeService $service;

    public function __construct(MarkingTypeService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(): Response
    {
        $markingTypes = MarkingType::orderBy('name')->getModels();

        return Inertia::render('Guide/MarkingType', [
            'markingTypes' => $markingTypes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->create($request);
        return redirect()->back()->with('success', 'Маркировка добавлена');
    }

    public function update(Request $request, MarkingType $markingType): RedirectResponse
    {
        $this->service->update($markingType, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(MarkingType $markingType): RedirectResponse
    {
        $this->service->destroy($markingType);
        return redirect()->back()->with('success', 'Маркировка удалена');
    }
}
