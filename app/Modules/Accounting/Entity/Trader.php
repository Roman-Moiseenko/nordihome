<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Traits\ActiveFieldModel;
use App\Modules\Guide\Entity\VAT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property int $id
 * @property string $name
 * @property bool $default
 * @property bool $active
 * @property Organization $organization
 * @property Organization[] $organizations
 */
class Trader extends Model
{
    use ActiveFieldModel;

    protected $fillable = [
        'name',
        'active'
    ];

    public static function register(string $name): self
    {
        return self::create(['name' => $name]);
    }

    public static function default(): self
    {
        return self::where('default', true)->first();
    }

    public function organization(): HasOneThrough
    {
        return $this->hasOneThrough(Organization::class, TraderOrganization::class,
            'trader_id', 'id',
            'id', 'organization_id')
            ->where('trader_organizations.default', true);
        //return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }


    public function organizations(): BelongsToMany
    {
        return $this->
        belongsToMany(Organization::class, 'trader_organizations', 'trader_id', 'organization_id', 'id', 'id')
            ->withPivot(['default']);
    }

}
