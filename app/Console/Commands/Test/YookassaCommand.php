<?php

namespace App\Console\Commands\Test;

use App\Modules\Bank\Service\YookassaService;
use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;
use App\Modules\Unload\Entity\Feed;
use App\Modules\Unload\Repository\FeedRepository;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class YookassaCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'test:test';
    protected $description = 'Тестируем';

    public function handle(FeedRepository $repository)
    {
        $feed = Feed::find(1);
        $repository->GetProducts($feed);

/*
        $leads = Lead::get();
        foreach ($leads as $lead) {
            $data = $lead->data;
            $this->info($lead->id);
        }
*/
    }
}
