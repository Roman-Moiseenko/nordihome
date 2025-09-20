<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property BannerWidgetItem[] $items
 */
class BannerWidget extends Widget
{

    protected $table="widget_banners";

    public function itemBySlug(string $slug): ?BannerWidgetItem
    {
        foreach ($this->items as $item) {
            if ($item->slug = $slug) return $item;
        }
        return null;
    }

    public function items(): HasMany
    {
        return $this->hasMany(BannerWidgetItem::class, 'widget_id', 'id')->orderBy('sort');
    }

    #[Deprecated]
    public static function findView(int $id): string
    {
        /** @var BannerWidget $banner */
        $banner = self::find($id);
        if (is_null($banner)) return '';
        return $banner->view();
    }

    #[Deprecated]
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

    #[Deprecated]
    public function view(): string
    {
        return view( Template::blade('banner') . $this->template, ['banner' => $this])->render();
    }
}
