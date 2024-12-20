<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

abstract class AccountingReport
{
    abstract public function index(): array;

    final public function renderArray(array $items): array
    {
        $result = [];
        foreach ($items as $key => $value) {
            $result[] = [
                'method' => $key,
                'class' => static::class,
                'label' => $value,
            ];
        }
        return $result;
    }


}
