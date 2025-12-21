<?php

namespace App\Console\Commands;

use App\Modules\Base\Entity\Photo;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Tests\CreatesApplication;

class TestCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'test:test';
    protected $description = 'Тестируем';

    public function handle(ParserIkea $service): void
    {

        $categories = CategoryParser::where('slug', null)->getModels();
        $this->info('Найдено ' . count($categories));
        foreach ($categories as $i => $category) {
            $category->slug = Str::slug($category->name);
            $category->save();
            $this->info($i);
        }

/*
        $photos = Photo::where('type', 'image')
            ->where('thumb', false)
            //->where('imageable_type', '<>', 'App\\Modules\\Parser\\Entity\\CategoryParser')
            ->getModels();
        $this->info('Найдено ' . count($photos));
        //dd('1');
        foreach ($photos as $i => $photo) {
            $photo->setThumb(true);
            $this->info($i);
        }
        $this->info('Обработано!');
        */
     /*   $pricing = PricingDocument::first();
        foreach ($pricing->pricingProducts as $pricingProduct) {
            $pricingProduct->price_cost = $pricingProduct->price_retail / 2;
            $pricingProduct->price_pre = $pricingProduct->price_retail;
            $pricingProduct->save();
        }
        $pricingService = new PricingService();

        $pricingService->completed($pricing);*/
/*
        $leads = Lead::get();
        foreach ($leads as $lead) {
            $data = $lead->data;
            $this->info($lead->id);
        }
*/


    }
}
