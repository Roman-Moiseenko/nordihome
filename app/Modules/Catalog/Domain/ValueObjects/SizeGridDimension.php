<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\ValueObjects;

/**
 * Измерение размерной сетки (например: 'size' → 'Размер', 'width' → 'Полнота').
 */
final class SizeGridDimension
{
    public const DIMENSION_SIZE = 'size';
    public const DIMENSION_WIDTH = 'width';

    /**
     * Машинный код измерения: 'size', 'width'. Используется в БД как dimension_code.
     */
    public readonly string $code;

    /**
     * Человекочитаемое имя для админки и фронта: 'Размер', 'Полнота'.
     */
    public readonly string $name;

    public function __construct(
        string $code,
        string $name,
    ) {
        if (trim($code) === '') {
            throw new \InvalidArgumentException('Код измерения не может быть пустым');
        }

        if (trim($name) === '') {
            throw new \InvalidArgumentException('Название измерения не может быть пустым');
        }

        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Все доступные измерения размерной сетки.
     *
     * @return SizeGridDimension[]
     */
    public static function available(): array
    {
        return [
            new self(self::DIMENSION_SIZE, 'Размер'),
            new self(self::DIMENSION_WIDTH, 'Полнота'),
        ];
    }

    /**
     * Найти измерение по коду.
     */
    public static function findByCode(string $code): ?self
    {
        foreach (self::available() as $dimension) {
            if ($dimension->code === $code) {
                return $dimension;
            }
        }

        return null;
    }

    /**
     * Создать VO из массива ['code' => ..., 'name' => ...].
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (string) ($data['code'] ?? ''),
            name: (string) ($data['name'] ?? ''),
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array{code: string, name: string}
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
