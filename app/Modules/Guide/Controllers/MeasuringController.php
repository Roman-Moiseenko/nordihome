<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Service\MeasuringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MeasuringController extends Controller
{
    private MeasuringService $service;

    public function __construct(MeasuringService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(): Response
    {
        $measurings = Measuring::orderBy('name')->getModels();

        return Inertia::render('Guide/Measuring', [
            'measurings' => $measurings,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->create($request);
        return redirect()->back()->with('success', 'Ед.измерения добавлена');
    }

    public function update(Request $request, Measuring $measuring): RedirectResponse
    {
        $this->service->update($measuring, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Measuring $measuring): RedirectResponse
    {
        $this->service->destroy($measuring);
        return redirect()->back()->with('success', 'Ед.измерения удалена');
    }
}
