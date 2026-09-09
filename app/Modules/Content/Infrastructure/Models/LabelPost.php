<?php
declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $label_id
 * @property int $post_id
 * @property Post $post
 * @property Label $label
 */
class LabelPost extends Model
{
    public $timestamps = false;
    protected $table = 'labels_posts';
}
