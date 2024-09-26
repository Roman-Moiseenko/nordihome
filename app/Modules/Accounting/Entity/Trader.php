<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Traits\ActiveFieldModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property int $organization_id
 * @property bool $default
 * @property bool $active
 * @property Organization $organization
 */
class Trader extends Model
{
    use ActiveFieldModel;

    protected $fillable = [
        'name',
        'organization_id'
    ];

    public static function register(string $name): self
    {
        return self::create(['name' => $name]);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
