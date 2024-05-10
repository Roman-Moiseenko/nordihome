<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Modules\Admin\Entity\Worker;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $id
 * @property string $name
 * @property int $worker_id
 * @property float $weight
 * @property float $volume
 * @property bool $cargo
 * @property bool $service
 * @property bool $active
 * @property Worker $worker
 */
class DeliveryTruck extends Model
{
    public $timestamps = false;
    protected $attributes = [
        'cargo' => true,
        'service' => true,
        'worker_id' => null,
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
    //*** IS-.....
    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function isBlocked(): bool
    {
        return $this->active == false;
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

    //*** GET-....

    #[Pure]
    public function getNameWorker(): string
    {
        if (is_null($this->worker)) return 'Не назначен';
        return $this->worker->fullname->getFullName();
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'id');
    }
}
