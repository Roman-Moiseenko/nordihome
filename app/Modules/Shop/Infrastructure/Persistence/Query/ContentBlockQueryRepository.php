<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Elements\WidgetPageData;
use App\Modules\Shop\Application\DTOs\PageElements\ContentBlockPageData;
use Illuminate\Support\Facades\DB;

class ContentBlockQueryRepository
{
    /**
     * Получить все активные контент-блоки для указанного контейнера
     * с присоединёнными данными виджета и его инстанса.
     * Сортировка по полю sort_order.
     *
     * @param string $containerType  Тип контейнера ('post' | 'page')
     * @param int    $containerId    ID записи или страницы
     *
     * @return ContentBlockPageData[]
     */
    public function getBlocksByContainer(string $containerType, int $containerId): array
    {
        $rows = DB::table('content_blocks')
            ->where('content_blocks.container_type', $containerType)
            ->where('content_blocks.container_id', $containerId)
            ->where('content_blocks.active', true)
            ->join('widget_instances', 'content_blocks.widget_instance_id', '=', 'widget_instances.id')
            ->join('widgets', 'widget_instances.widget_id', '=', 'widgets.id')
            ->select(
                'content_blocks.section',
                'widgets.slug as widget_slug',
                'widgets.category as widget_category',
                'widget_instances.params',
            )
            ->orderBy('content_blocks.sort_order')
            ->get();

        $blocks = [];
        foreach ($rows as $row) {
            $blocks[] = new ContentBlockPageData(
                section: $row->section ?? '',
                widget: new WidgetPageData(
                    category: $row->widget_category ?? '',
                    slug: $row->widget_slug,
                    params: json_decode($row->params ?? '{}', true),
                ),
            );
        }

        return $blocks;
    }
}
