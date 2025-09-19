<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $banner_id
 * @property string $url
 * @property string $caption
 * @property string $description
 * @property int $sort
 * @property string $slug
 *
 * @property BannerWidget $banner
 */
class BannerWidgetItem extends Model
{
    use ImageField;
    public $timestamps = false;

    protected $table= "banner_items";

    protected $fillable = [
        'banner_id',
        'sort',
    ];

    public static function register(int $banner_id): self
    {
        return self::create([
            'banner_id' => $banner_id,
            'sort' => self::where('banner_id', $banner_id)->count(),
        ]);
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(BannerWidget::class, 'banner_id', 'id');
    }
}
