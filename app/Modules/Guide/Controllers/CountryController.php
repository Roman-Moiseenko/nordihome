<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Service\CountryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    private CountryService $service;

    public function __construct(CountryService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(): Response
    {
        $countries = Country::orderBy('name')->getModels();
        return Inertia::render('Guide/Country', [
            'countries' => $countries,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->create($request);
        return redirect()->back()->with('success', 'Страна добавлена');
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $this->service->update($country, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Country $country): RedirectResponse
    {
        $this->service->destroy($country);
        return redirect()->back()->with('success', 'Страна удалена');
    }
}
