<?php

namespace App\Modules\Content\Repository;

use App\Modules\Content\Entity\Widgets\PostWidget;
use Illuminate\Http\Request;

class PostWidgetRepository
{

    public function getIndex(Request $request)
    {
        return PostWidget::orderBy('name')->get()->map(fn(PostWidget $widget) => array_merge($widget->toArray(), [
        ]));
    }

    public function PostWithToArray(PostWidget $widget): array
    {
        return array_merge($widget->toArray(), [
            'image' => $widget->getImage(),
            'icon' => $widget->getIcon(),
            'category' => [
                'name' => is_null($widget->category_id) ? 'Все' : $widget->category->name,
            ]
        ]);
    }
}
