<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

class DataWidget
{
    public string $title = '';
    public string $url = '';
    public ?\App\Modules\Base\Entity\Photo $image = null;
    public string $description = '';
    public array $items = [];
    public string $template = '';
}
