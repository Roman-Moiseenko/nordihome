<?php
declare(strict_types=1);

namespace App\Modules\Pages\Entity;

interface DataWidgetInterface
{
    public function getDataWidget(array $params = []): DataWidget;
}
