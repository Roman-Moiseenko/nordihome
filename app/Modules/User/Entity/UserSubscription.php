<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $notification_id
 */
class UserSubscription extends Model
{
    public $timestamps = false;
    protected $table = 'users_subscriptions';

}
