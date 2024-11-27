<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Series;
use App\Modules\Product\Service\SeriesService;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    private SeriesService $service;

    public function __construct(SeriesService $service)
    {
        //$this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Series::orderBy('name');
        $list = $this->pagination($query, $request, $pagination);
        return view('admin.product.series.index', compact('list', 'pagination'));
    }

    public function store(Request $request)
    {
        $series = $this->service->create($request['name']);
        return redirect()->route('admin.product.series.show', compact('series'));
    }

    public function update(Series $series, Request $request)
    {
        $this->service->update($series, $request['name']);
        return redirect()->route('admin.product.series.show', compact('series'));
    }

    public function show(Series $series)
    {
        return view('admin.product.series.show', compact('series'));
    }

    public function add_product(Request $request, Series $series)
    {
        $this->service->add_product($series, (int)$request['product_id']);
        return redirect()->route('admin.product.series.show', compact('series'));
    }

    public function add_products(Request $request, Series $series)
    {
        $this->service->add_products($series, $request['products']);
        return redirect()->route('admin.product.series.show', compact('series'));
    }

    public function del_product(Request $request, Series $series)
    {
        $this->service->remove_product($series, (int)$request['product_id']);
        return redirect()->back();
    }

    public function destroy(Series $series)
    {
        $this->service->remove($series);
        return redirect()->back();
    }
}
