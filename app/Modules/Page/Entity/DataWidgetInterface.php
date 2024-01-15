<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

interface DataWidgetInterface
{
    public function getDataWidget(array $params = []): DataWidget;
}
