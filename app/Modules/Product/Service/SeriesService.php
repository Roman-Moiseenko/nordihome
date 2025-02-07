<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use Illuminate\Http\Request;

class SeriesService
{
    public function create(string $name): Series
    {
        $series = Series::where('name', $name)->first();
        if (is_null($series)) $series = Series::register($name);
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

    public function addProduct(Series $series, int $product_id): void
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        $product->series_id = $series->id;
        $product->save();
    }

    public function addProducts(Series $series, array $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($series,
                $product['product_id'],
            );
        }
    }

    public function remove_product(Series $series, int $product_id)
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        if ($series->id != $product->series_id) throw new \DomainException('Не совпадение серий');
        $product->series_id = null;
        $product->save();
    }

    public function remove(Series $series): void
    {
        foreach ($series->products as $product) {
            $product->series_id = null;
            $product->save();
        }
        $series->delete();
    }

    public function update(Series $series, string $name)
    {
        $series->name = $name;
        $series->save();
    }

}
