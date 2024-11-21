<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\User\Entity\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * @property int $user_id
 * @property int $organization_id
 * @property boolean $default
 * @property User $user
 * @property Organization $organization
 */
class ShopperOrganization extends Model
{
    public $timestamps = false;

    protected $casts = [
        'default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
