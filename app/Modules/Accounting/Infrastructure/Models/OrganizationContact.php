<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $organization_id
 * @property string $post
 * @property string $fullname
 * @property string $phone
 * @property string $email
 * @property Organization $organization
 */
class OrganizationContact extends Model
{
    protected $touches = ['organization'];

    protected $attributes = [
        'fullname' => '',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
