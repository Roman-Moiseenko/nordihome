<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Transport;

use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class DeliveryData
{
    public float $cost;
    public int $days;

    public function __construct(float $cost, int $days)
    {
        $this->cost = $cost;
        $this->days = $days;
    }
}
