<?php

namespace App\Modules\Parser\Job;

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateParserProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $product;

    public function __construct(array $product)
    {

        $this->product = $product;
    }


    public function handle(ParserIkea $parserIkea): void
    {
        $parserIkea->createProductJob($this->product);
    }
}
