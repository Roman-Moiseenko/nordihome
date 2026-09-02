<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity\Widgets;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Content\Domain\ValueObjects\ProductGroupType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $model_id
 * @property string $model_type
 * @property CatalogWidget $widget
 */
class CatalogWidgetItem extends WidgetItem
{

    protected $table='widget_catalog_items';
    protected $fillable = [
        'model_id',
        'model_type',
    ];

    public static function register(int $widgetId, int $model_id, string $model_type): self
    {
        $item = parent::new($widgetId);
        $item->model_id = $model_id;
        $item->model_type = $model_type;
        $item->slug = "$model_type-$model_id";
        $item->save();
        return $item;
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(CatalogWidget::class, 'widget_id', 'id');
    }

    public function getModel()
    {
        $class = ProductGroupType::modelClass($this->model_type);
        return $class::find($this->model_id);
    }
    public function url(): string
    {
        $model = $this->getModel();
        return route('shop.' . $this->model_type . '.view', $model->slug);
    }

    public function image():? string
    {
        $model = $this->getModel();
        return $model->getImage();
    }

    public function name(): string
    {
        $model = $this->getModel();
        return $model->name;
    }

}
