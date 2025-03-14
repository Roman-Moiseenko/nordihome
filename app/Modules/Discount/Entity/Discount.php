<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use App\Modules\Shop\Cart\CartItem;
use App\Modules\Shop\CartItemInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $discount - скидка в %%
 * @property string $name
 * @property string $title
 * @property string $class //$class
 * @property string $_from //миним.значение
 * @property string $_to //максим.значение в условии
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */

class Discount extends Model
{
    const TYPE = 'Скидка';

    protected $table ='discounts';
    protected $fillable = [
        'discount',
        'name',
        'title',
        'active',
        'class',
        '_from',
        '_to'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $name, string $class,): self
    {
        return self::create([
            'discount' => 0,
            'name' => $name,
            'title' => '',
            'active' => false,
            'class' => $class,
            '_from' => '',
            '_to' => '',
        ]);
    }

    /**
     * @param CartItemInterface[] $items
     * @param bool $written
     * @return int
     */
    public function render(array &$items, bool $written = true): int
    {
        if (!$this->active) throw new \DomainException('Неверный алгоритм - текущий Discount (' . $this->id . ') не активен');
        $amount = array_sum(array_map(function ($item) {
            return ($item->getCheck())
                ? $item->getSellCost() * $item->getQuantity()
                : 0;
        }, $items));

        //if ($amount == 0) return 0; //Все элементы со скидкой

        if ($this->isEnabled($amount)) {
            if ($written) {
                array_walk($items, function (CartItemInterface &$item) {
                    if ($item->getCheck()) {
                        $item->setDiscount($this->id);
                        $item->setDiscountType(Discount::class);
                        $item->setSellCost(round((($item->getBaseCost()) * (100 - $this->discount)) / 100));
                        $item->setDiscountName(empty($this->title) ? '' : $this->title . ' (' . $this->discount . '%)');
                    }
                });
            }
            return $this->discount;
        }
        return 0;
        //if $written - то массив $items перезаписывается, в discount устанавливается посчитанная скидка
    }

    public function isEnabled(float $cost = null): bool
    {
        $class = __NAMESPACE__ . "\\" . $this->class;
        return $class::isEnabled($this, $cost);
    }

    public function isActive(): bool
    {
        return $this->active == true;
    }

    public static function namespace(): string
    {
        return __NAMESPACE__;
    }

    public function nameType(): string
    {
        $class = __NAMESPACE__ . "\\" . $this->class;
        return $class::name();
    }

    public function caption(): string
    {
        $class = __NAMESPACE__ . "\\" . $this->class;
        return $class::caption($this->_from) . ' - ' . $class::caption($this->_to);
    }

}
