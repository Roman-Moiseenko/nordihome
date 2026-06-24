<?php

namespace App\Modules\Auth\Domain\ValueObjects;

use InvalidArgumentException;

final class StaffPositions
{
    /** @var StaffPosition[] */
    private array $positions;

    public function __construct(array $positions)
    {
        if (empty($positions)) {
            throw new InvalidArgumentException('Должна быть указана хотя бы одна должность');
        }

        $this->positions = array_map(
            fn(string $pos) => new StaffPosition($pos),
            array_unique($positions) // исключаем дубликаты
        );
    }

    /** @return StaffPosition[] */
    public function getPositions(): array
    {
        return $this->positions;
    }

    /** @return string[] */
    public function toArrayOfStrings(): array
    {
        return array_map(fn(StaffPosition $p) => $p->getValue(), $this->positions);
    }

    public function contains(StaffPosition $position): bool
    {
        foreach ($this->positions as $p) {
            if ($p->equals($position)) return true;
        }
        return false;
    }

    public function equals(self $other): bool
    {
        return $this->toArrayOfStrings() === $other->toArrayOfStrings();
    }
}
