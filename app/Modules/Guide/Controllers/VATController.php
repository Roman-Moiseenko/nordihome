<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Guide\Service\VATService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VATController extends Controller
{
    private VATService $service;

    public function __construct(VATService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(): Response
    {
        $VATs = VAT::orderBy('value')->getModels();

        return Inertia::render('Guide/VAT', [
            'VATs' => $VATs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->create($request);
        return redirect()->back()->with('success', 'Налог добавлен');
    }

    public function update(Request $request, VAT $vat): RedirectResponse
    {
        $this->service->update($vat, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(VAT $vat): RedirectResponse
    {
        $this->service->destroy($vat);
        return redirect()->back()->with('success', 'Налог удален');
    }
}
