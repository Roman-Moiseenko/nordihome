<?php

declare(strict_types=1);

namespace App\Modules\Parser\Domain\ValueObjects;

final class Store
{
    /**
     * Коды магазинов ИКЕА (Польша) и их названия.
     *
     * @var array<int, string>
     */
    public const array STORES = [
        203 => 'Гданьск',
        188 => 'Рашин',
        204 => 'Краков',
        205 => 'Познань',
        294 => 'Бабжице',
        306 => 'Катовице',
        307 => 'Варшава',
        311 => 'Люблин',
        329 => 'Лодзь',
        429 => 'Быдгощ',
        583 => 'Щецин',
    ];

    private function __construct(
        private readonly int $code,
        private readonly string $name,
        private readonly int $quantity,
    ) {
    }

    public static function fromCodeAndQuantity(int $code, int $quantity): self
    {
        return new self($code, self::name($code), $quantity);
    }

    public static function name(int $code): string
    {
        return self::STORES[$code] ?? 'Неизвестный магазин #' . $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Преобразование ассоциативного массива ['код_магазина' => кол-во] в массив Store.
     *
     * @param array<int|string, int> $associative
     * @return Store[]
     */
    public static function collectionFromAssociative(array $associative): array
    {
        $stores = [];
        foreach ($associative as $code => $quantity) {
            $stores[] = self::fromCodeAndQuantity((int) $code, (int) $quantity);
        }
        return $stores;
    }

    /**
     * Преобразование массива Store в ассоциативный массив ['код_магазина' => кол-во].
     *
     * @param Store[] $stores
     * @return array<int, int>
     */
    public static function collectionToAssociative(array $stores): array
    {
        $associative = [];
        foreach ($stores as $store) {
            $associative[$store->getCode()] = $store->getQuantity();
        }
        return $associative;
    }
}
