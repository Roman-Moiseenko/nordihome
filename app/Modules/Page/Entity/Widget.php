<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Product\Entity\Group;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property bool $active // -?
 * @property string $data_class
 * @property int $data_id
 * @property string $template
 * @property array $params
 */
class Widget extends Model
{
    const PATH_TEMPLATES = 'admin.page.widget.template.';
    const WIDGET_CLASSES = [
        'Акция' => Promotion::class,
        'Группа товаров' => Group::class,
        //'Товар' => Product::class,
      //  'Банер (В разработке)' => '1',
     //   'Карта (В разработке)' => '2'
    ];

    const WIDGET_TEMPLATES = [
        'promotion-4product' => 'Акция + 4 товара',
        'row-4product' => 'Список товаров, 4 в ряд (1 ряд)',
        'row-6product' => 'Список товаров, 6 в ряд (множество рядов)',
    ];

    public $timestamps = false;
    public $fillable = [
        'name',
        'data_class',
        'data_id',
        'active',
        'template',
        'params',
    ];

    protected $casts = [
        'params' => 'json'
    ];


    public static function register(string $name, string $data_class, int $data_id, string $template, array $params): self
    {
        return self::create([
            'name' => $name,
            'data_class' => $data_class,
            'data_id' => $data_id,
            'active' => true,
            'template' => $template,
            'params' => $params,
        ]);
    }

    public function view(): string
    {
        $dataItem = $this->DataWidget();
        return view(self::PATH_TEMPLATES . $this->template, ['widget' => $dataItem])->render();
    }

    public function getName(): string
    {
        return $this->DataWidget()->title;
    }

    public function getObject(): string
    {
        return array_search($this->data_class, self::WIDGET_CLASSES);
    }

    public function templateName():string
    {
        return self::WIDGET_TEMPLATES[$this->template];
    }

    private function DataWidget(): DataWidget
    {
        /** @var DataWidgetInterface $item */
        $item = ($this->data_class)::find($this->data_id);
        return $item->getDataWidget($this->params);
    }

    public function draft()
    {
        $this->active = false;
        $this->save();
    }

    public function activated()
    {
        $this->active = true;
        $this->save();
    }

}
