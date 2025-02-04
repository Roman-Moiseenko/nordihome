<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Modules\Admin\Entity\Worker;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $id
 * @property string $name
 * @property float $weight
 * @property float $volume
 * @property bool $active
 */
class DeliveryTruck extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'weight',
        'volume',
        'active',
    ];

    public static function register(string $name, float $weight, float $volume): self
    {
        return self::create([
            'name' => $name,
            'weight' => $weight,
            'volume' => $volume,
            'active' => true,
        ]);
    }
    //*** IS-.....
    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function isBlocked(): bool
    {
        return $this->active == false;
    }

    public function draft(): void
    {
        $this->active = false;
        $this->save();
    }

    //*** GET-....

    public function active(): void
    {
        $this->active = true;
        $this->save();
    }
}
