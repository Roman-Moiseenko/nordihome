<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $trader_id
 * @property int $organization_id
 * @property boolean $default
 * @property Trader $trader
 * @property Organization $organization
 */
class TraderOrganization extends Model
{
    public $timestamps = false;

    protected $casts = [
        'default' => 'boolean',
    ];

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

}
