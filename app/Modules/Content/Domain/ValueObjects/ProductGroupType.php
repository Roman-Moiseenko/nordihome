<?php

declare(strict_types=1);

namespace App\Modules\Content\Domain\ValueObjects;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Entity\Series;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use InvalidArgumentException;

/**
 * ProductGroupType — тип сущности, из которой виджет берёт группу товаров.
 *
 * Ключ используется в JSON Schema (поле entity_type объекта «Группа товаров»),
 * label — человекочитаемое название для админки, model — класс Eloquent-модели,
 * через который впоследствии сопоставляются товары выбранной сущности.
 *
 * Список легко расширяется добавлением нового элемента в self::TYPES.
 */
final class ProductGroupType
{
    public const string CATEGORY = 'category';
    public const string ROOM = 'room';
    public const string GROUP = 'group';
    public const string PROMOTION = 'promotion';
    public const string SERIES = 'series';

    /**
     * @var array<string, array{label: string, model: class-string}>
     */
    public const array TYPES = [
        self::CATEGORY => [
            'label' => 'Категория',
            'model' => Category::class,
        ],
        self::ROOM => [
            'label' => 'Комната',
            'model' => Room::class,
        ],
        self::GROUP => [
            'label' => 'Группа',
            'model' => Group::class,
        ],
        self::PROMOTION => [
            'label' => 'Акция',
            'model' => Promotion::class,
        ],
        self::SERIES => [
            'label' => 'Серия',
            'model' => Series::class,
        ],
    ];

    private string $value;

    public function __construct(string $value)
    {
        $normalized = strtolower(trim($value));

        if (!self::supports($normalized)) {
            throw new InvalidArgumentException("Недопустимый тип группы товаров: {$value}");
        }

        $this->value = $normalized;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return self::TYPES[$this->value]['label'];
    }

    /**
     * @return class-string
     */
    public function getModelClass(): string
    {
        return self::TYPES[$this->value]['model'];
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * @return array<string, string> Карта [код => название] для выпадающего списка.
     */
    public static function labels(): array
    {
        return array_map(
            static fn(array $type): string => $type['label'],
            self::TYPES
        );
    }

    /**
     * @return class-string|null
     */
    public static function modelClass(string $value): ?string
    {
        $normalized = strtolower(trim($value));

        return self::TYPES[$normalized]['model'] ?? null;
    }

    public static function models(): array
    {
        return array_map(
            static fn(array $type): string => $type['model'],
            self::TYPES
        );
    }
    public static function label(string $value): ?string
    {
        $normalized = strtolower(trim($value));

        return self::TYPES[$normalized]['label'] ?? null;
    }

    public static function supports(string $value): bool
    {
        return isset(self::TYPES[strtolower(trim($value))]);
    }
}
