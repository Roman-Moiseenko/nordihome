<?php
declare(strict_types=1);

namespace App\Modules\Shop\Calculate;

interface CalculateInterface
{
    public function getCost(array $items): Cost;
}
