<?php
declare(strict_types=1);

namespace App\Modules\Pages\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property string $tag
 */
class Page extends Model
{

}
