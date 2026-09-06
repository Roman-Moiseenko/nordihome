<?php
declare(strict_types=1);

namespace App\Modules\Guide\Infrastructure\Models;

use App\Modules\Order\Entity\Addition\CalculateAddition;
use App\Modules\Order\Infrastructure\Models\OrderAddition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @property int $id
 * @property string $name
 * @property int $base
 * @property string $type
 * @property bool $manual
 * @property bool $is_quantity
 * @property string $slug
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
        'slug',
    ];

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
