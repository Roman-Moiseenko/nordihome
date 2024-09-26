<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Entity\FullName;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $organization_id
 * @property string $post
 * @property FullName $fullname
 * @property string $phone
 * @property string $email
 * @property Organization $organization
 */
class OrganizationContact extends Model
{
    protected $touches = ['organization'];

    protected $attributes = [
        'fullname' => '{}',
    ];
    protected $casts = [
        'fullname' => FullNameCast::class,
    ];

    public static function new(FullName $fullName): self
    {
        $contact = self::make();
        $contact->fullname = $fullName;
        return $contact;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
