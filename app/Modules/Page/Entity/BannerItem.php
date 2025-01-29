<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $banner_id
 * @property string $url
 * @property string $caption
 * @property string $description
 */
class BannerItem extends Model
{
    use ImageField;
    public $timestamps = false;

    public static function new(): self
    {
        return self::make([]);
    }
}
