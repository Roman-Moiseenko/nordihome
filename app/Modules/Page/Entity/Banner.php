<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property bool $active
 * @property string $name
 * @property string $template
 * @property string $caption
 * @property string $description
 * @property BannerItem[] $items
 */
class Banner extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'name',
        'active',
        'template',
    ];

    public static function register(string $name, string $template)
    {
        return self::create([
            'name' => $name,
            'template' => $template,
            'active' => false,
        ]);
    }

    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function items(): HasMany
    {
        return $this->hasMany(BannerItem::class, 'banner_id', 'id')->orderBy('sort');
    }

    public static function findView(int $id): string
    {
        /** @var Banner $banner */
        $banner = self::find($id);
        if (is_null($banner)) return '';
        return $banner->view();
    }

    public static function renderFromText(string|null $text): string
    {
        if (is_null($text)) return '';
        preg_match_all('/\[banner=\"(.+)\"\]/', $text, $matches);
        $replaces = $matches[0]; //шот-коды вида [widget="7"] (массив)
        $banner_ids = $matches[1]; //значение id виджета (массив)

        foreach ($banner_ids as $key => $banner_id) {
            $text = str_replace(
                $replaces[$key],
                self::findView((int)$banner_id),
                $text);
        }
        return $text;
    }


    public function view(): string
    {
        return view( Template::blade('banner') . $this->template, ['banner' => $this])->render();
    }
}
