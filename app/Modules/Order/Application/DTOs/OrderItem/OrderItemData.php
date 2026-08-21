<?php
declare(strict_types=1);

namespace App\Modules\Order\Application\DTOs\OrderItem;

readonly class OrderItemData
{
    public function __construct(
        public ?int    $productId = null,
        public ?float  $quantity = null,
        public ?float  $basePrice = null,
        public ?float  $sellPrice = null,
        public ?int    $discountId = null,
        public ?string $discountType = null,
        public ?bool   $preorder = null,
        public ?bool   $fixManual = null,
        public ?array  $options = null,
        public ?string $comment = null,
        public ?bool   $assemblage = null,
        public ?bool   $packing = null,
    ) {
    }
}
