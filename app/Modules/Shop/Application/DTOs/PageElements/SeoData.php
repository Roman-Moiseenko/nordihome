<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

class SeoData
{
    public function __construct(public string $title,
                                public string $description)
    {

    }
}
