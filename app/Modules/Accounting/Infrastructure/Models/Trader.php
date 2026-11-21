<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Models;

use App\Modules\Base\Traits\ActiveFieldModel;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $default
 * @property bool $active
 * @property Organization $organization
 * @property Organization[] $organizations
 */
class Trader extends Model
{
    use ActiveFieldModel, ImageField;

    protected $fillable = [
        'name',
        'active'
    ];

    public function organization(): HasOneThrough
    {
        return $this->hasOneThrough(Organization::class, TraderOrganization::class,
            'trader_id', 'id',
            'id', 'organization_id')
            ->where('trader_organizations.default', true);
    }

    public function organizations(): BelongsToMany
    {
        return $this->
        belongsToMany(Organization::class, 'trader_organizations', 'trader_id', 'organization_id', 'id', 'id')
            ->withPivot(['default']);
    }

}
