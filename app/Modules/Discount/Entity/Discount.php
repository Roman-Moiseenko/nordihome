<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use App\Modules\Shop\Cart\CartItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $discount
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
    protected $table ='discounts';
    protected $fillable = [
        'discount',
        'name',
        'title',
        //'active',
        'class',
        '_from',
        '_to'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $name, string $title, int $discount, string $class, string $_from, string $_to): self
    {
        return self::create([
            'discount' => $discount,
            'name' => $name,
            'title' => $title,
            'active' => false,
            'class' => $class,
            '_from' => $_from,
            '_to' => $_to,
        ]);
    }

    /**
     * @param CartItem[] $items
     * @param bool $written
     * @return int
     */
    public function render(array &$items, bool $written = true): int
    {
        if (!$this->active) throw new \DomainException('Неверный алгоритм - текущий Discount (' . $this->id . ') не активен');
        $amount = array_sum(array_map(function ($item) {
            return (empty($item->discount_cost) && $item->check)
                ? $item->base_cost * $item->quantity
                : 0;
        }, $items));

        if ($amount == 0) return 0; //Все элементы со скидкой

        if ($this->isEnabled($amount)) {
            if ($written) {
                array_walk($items, function (&$item) {
                    if (empty($item->discount_cost) && $item->check) {
                        $item->discount_id = $this->id;
                        $item->discount_cost = round((($item->base_cost) * (100 - $this->discount)) / 100);
                        $item->discount_name = empty($this->title) ? '' : $this->title . ' (' . $this->discount . '%)';
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

    public function active(): bool
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
