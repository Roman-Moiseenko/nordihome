<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

abstract class AccountingReport
{
    abstract public function index(): array;

    final public function renderArray(array $items): array
    {
        $array = array_select($items);

        return array_map(function ($item) {
            return [
                'label' => $item['label'],
                'value' => static::class . '::' . $item['label'],
            ];
        }, $array);
    }
}
