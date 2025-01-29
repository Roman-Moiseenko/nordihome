<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Guide\Entity\CargoCompany;
use App\Modules\Guide\Service\AdditionService;
use App\Modules\Guide\Service\CargoCompanyService;
use App\Modules\Order\Entity\Addition\CalculateAddition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CargoCompanyController extends Controller
{

    private CargoCompanyService $service;

    public function __construct(CargoCompanyService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(): Response
    {
        $cargo_companies = CargoCompany::orderBy('name')->get();

        return Inertia::render('Guide/CargoCompany', [
            'cargo_companies' => $cargo_companies,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->create($request);
        return redirect()->back()->with('success', 'Транспортная компания добавлена');
    }

    public function update(Request $request, CargoCompany $cargo): RedirectResponse
    {
        $this->service->update($cargo, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(CargoCompany $cargo): RedirectResponse
    {
        $this->service->destroy($cargo);
        return redirect()->back()->with('success', 'Транспортная компания удалена');
    }


}
