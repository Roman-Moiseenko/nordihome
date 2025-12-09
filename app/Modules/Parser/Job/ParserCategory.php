<?php

namespace App\Modules\Parser\Job;

use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Создаем парсер категорию и распарсиваем ее дочерние
 */
class ParserCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $category;

    public function __construct(array $category)
    {

        $this->category = $category;
    }

    public function handle(ParserIkea $parserIkea): void
    {
        $parserIkea->addCategory($this->category);

    }
}
