<?php

namespace App\Console\Commands\IKEA;

use App\Console\CreatesApplication;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestParseCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'test:parser-product';
    protected $description = 'Парсим Товары Икеа';

    public function handle(): void
    {
        /*
        Log::debug('IkeaProductCommand: Начало парсинга');
        $category = CategoryParser::where('url', '20611')->first();

        //Получить список товаров в категории
        Log::debug('IkeaProductCommand: Парсим категорию ' . $category->name . ' ' . $category->url);
        ParserProductsByCategory::dispatch($category);

        $categories = CategoryParser::where('slug', null)->get();
        foreach ($categories as $category) {

            $category->slug = $this->getSlug($category);
            $category->save();
        }
        $products = ProductParser::where('slug', null)->get();
        foreach ($products as $product) {
            $product->slug = $product->product->slug;
            $product->save();
        }

        */
    }

    private function getSlug(ParserCategory $category): string
    {
        $name = $category->name;
        $slug = Str::slug($name);
        if (!is_null(ParserCategory::where('slug', $slug)->first())) {
            $slug .= '-' . $category->ikea_id;
        }
        return $slug;
    }
}
