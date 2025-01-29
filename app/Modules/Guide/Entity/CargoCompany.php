<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use App\Modules\Delivery\Entity\DeliveryCargo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $url ссылка на трек номер
 * @property DeliveryCargo[] $deliveries
 */
class CargoCompany extends Model
{
    protected $table = 'guide_cargo_company';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
    ];

    public static function register(string $name, string $url = ''): self
    {
        return self::create([
            'name' => $name,
            'url' => $url,
        ]);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(DeliveryCargo::class, 'cargo_company_id', 'id');
    }
}
