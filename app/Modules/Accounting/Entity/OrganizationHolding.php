<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 *
 * @property Organization[] $organizations
 */
class OrganizationHolding extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public static function register(string $name): self
    {
        return self::create(['name' => $name]);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'holding_id', 'id');
    }
}
