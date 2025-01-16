<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use App\Modules\Order\Entity\Addition\CalculateAddition;
use App\Modules\Order\Entity\Order\OrderAddition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @property int $id
 * @property string $name
 * @property int $base
 * @property int $type
 * @property bool $manual
 * @property bool $is_quantity
 * @property string $class Class Обсчета стоимости
 * @property OrderAddition[] $orderAdditions
 */
class Addition extends Model
{
    protected $table = 'guide_addition';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'manual',
        'type',
        'base',
        'class',
        'is_quantity',
    ];
    const DELIVERY = 102;
    const PACKING = 103;
    const ASSEMBLY = 104;
    const LIFTING = 105;
    const OTHER = 109;

    const TYPES = [
        self::DELIVERY => 'Доставка', //Автоматическая для Польши, По городу - фиксированная, ТК - ручная
        self::PACKING => 'Упаковка', //Автоматическая будет
        self::LIFTING => 'Подъем', //Ручная ... или автомат расчет за этаж два Вида, Подьем по лестнице, Подьем лифтом
        self::ASSEMBLY => 'Сборка', //Автоматическая по указаным товарам
        self::OTHER => 'Другое',
    ];

    public static function register(
        string $name,
        #[ExpectedValues(valuesFromClass: Addition::class)]int $type,
        bool $manual,
        int $base,
        string $class = null,
        bool $is_quantity = false,
    ): self
    {
        return self::create([
            'name' => $name,
            'manual' => $manual,
            'type' => $type,
            'base' => $base,
            'class' => $class,
            'is_quantity' => false,
        ]);
    }

    public function typeName(): string
    {
        return self::TYPES[$this->type];
    }

    public function orderAdditions(): HasMany
    {
        return $this->hasMany(OrderAddition::class, 'addition_id', 'id');
    }

    public function className(): string
    {
        if (is_null($this->class)) return '';
        return CalculateAddition::CLASSES[$this->class];
    }
}
