<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Entity\Photo;

class DataWidget
{
    public string $title = '';
    public string $url = '';
    public ?Photo $image = null;
    public string $description = '';
    public array $items = [];
    public string $template = '';
}
