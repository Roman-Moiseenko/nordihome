<?php

namespace App\Modules\Shop\Application\DTOs\Parts;

class SeoData
{
    public function __construct(public string $title,
                                public string $description)
    {

    }
}
