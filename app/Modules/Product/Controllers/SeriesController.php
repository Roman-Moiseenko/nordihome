<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Series;
use App\Modules\Product\Repository\SeriesRepository;
use App\Modules\Product\Service\SeriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SeriesController extends Controller
{
    private SeriesService $service;
    private SeriesRepository $repository;

    public function __construct(SeriesService $service, SeriesRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $series = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Series/Index', [
            'series' => $series,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $series = $this->service->create($request['name']);
        return redirect()->route('admin.product.series.show', compact('series'));
    }

    public function show(Series $series): Response
    {
        return Inertia::render('Product/Series/Show', [
            'series' => $this->repository->SeriesToArray($series),
        ]);
    }

    public function add_product(Request $request, Series $series): RedirectResponse
    {
        try {
            $this->service->add_product($series, $request->integer('product_id'));
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_products(Request $request, Series $series): RedirectResponse
    {
        try {
            $this->service->add_products($series, $request->input('products'));
            return redirect()->back()->with('success', 'Товары добавлены');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(Request $request, Series $series): RedirectResponse
    {
        try {
            $this->service->remove_product($series, $request->integer('product_id'));
            return redirect()->back()->with('success', 'Товар удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Series $series): RedirectResponse
    {
        try {
            $this->service->remove($series);
            return redirect()->back()->with('success', 'Серия удалена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
