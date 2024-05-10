<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $worker_id
 * @property float $weight
 * @property float $volume
 * @property bool $cargo
 * @property bool $service
 * @property bool $active
 */
class DeliveryTruck extends Model
{
    public $timestamps = false;
    protected $attributes = [
        'cargo' => true,
        'service' => true,
        'staff_id' => null,
    ];
    protected $fillable = [
        'name',
        'worker_id',
        'weight',
        'volume',
        'cargo',
        'service',
        'active',
    ];

    public static function register(string $name, float $weight, float $volume, bool $cargo, bool $service): self
    {
        return self::create([
            'name' => $name,
            'weight' => $weight,
            'volume' => $volume,
            'cargo' => $cargo,
            'service' => $service,
            'active' => true,
        ]);
    }

    public function setDriver(int $worker_id)
    {
        $this->update(['worker_id' => $worker_id]);
    }

    public function draft()
    {
        $this->active = false;
        $this->save();
    }
}
