<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use Illuminate\Http\Request;

class SeriesService
{
    public function register(Request $request): Series
    {
        $series = Series::register($request['name']);
        return $series;
    }

    public function registerName(string $name): Series
    {
        $series = Series::register($name);
        return $series;
    }

    public function rename(Request $request, Series $series): Series
    {
        $series->update([
            'name' => $request['name'],
        ]);
        return $series;
    }

    public function delete(Series $series)
    {
        foreach ($series->products as $product) {
            $product->update(['series_id' => null]);
        }
        Series::destroy($series->id);
    }
}
