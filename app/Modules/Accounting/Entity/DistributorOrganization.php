<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $distributor_id
 * @property int $organization_id
 * @property boolean $default
 * @property Distributor $distributor
 * @property Organization $organization
 */
class DistributorOrganization extends Model
{
    public $timestamps = false;

    protected $casts = [
        'default' => 'boolean',
    ];

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
