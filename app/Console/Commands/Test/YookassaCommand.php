<?php

namespace App\Console\Commands\Test;

use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Bank\Service\YookassaService;
use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;
use App\Modules\Parser\Service\ParserIkea;
use App\Modules\Unload\Entity\Feed;
use App\Modules\Unload\Repository\FeedRepository;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class YookassaCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'test:test';
    protected $description = 'Тестируем';

    public function handle(ParserIkea $service): void
    {

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
