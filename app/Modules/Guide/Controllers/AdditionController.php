<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Guide\Service\AdditionService;
use App\Modules\Order\Entity\Addition\CalculateAddition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdditionController extends Controller
{

    private AdditionService $service;

    public function __construct(AdditionService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(): Response
    {
        $additions = Addition::orderBy('type')->get()->map(function (Addition $addition) {
            return array_merge($addition->toArray(), [
                'type_name' => $addition->typeName(),
                'class_name' => $addition->className(),
            ]);
        });
        $types = array_select(Addition::TYPES);
        $classes = array_select(CalculateAddition::CLASSES);

        return Inertia::render('Guide/Addition', [
            'additions' => $additions,
            'types' => $types,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->create($request);
        return redirect()->back()->with('success', 'Услуга добавлена');
    }

    public function update(Request $request, Addition $addition): RedirectResponse
    {
        $this->service->update($addition, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Addition $addition): RedirectResponse
    {
        $this->service->destroy($addition);
        return redirect()->back()->with('success', 'Услуга удалена');
    }


}
