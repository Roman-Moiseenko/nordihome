<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

interface DataWidgetInterface
{
    /**
     * Данные для виджета на экран. Используется для групп товаров, акции, баннеров и другое,
     * @param array $params
     * @return DataWidget
     */
    public function getDataWidget(array $params = []): DataWidget;
}
