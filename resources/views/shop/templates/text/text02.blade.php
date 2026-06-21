<!--template:Оповещение на главной вверху-->
@php
    /**
    * TextWidget::class - string
    * $widget->caption - string
    * $widget->description - string
    * $widget->image - Photo::class
    * $widget->icon - Photo::class
    * TextWidgetItem:class
    * $widget->items - Arraible
    * $widget->itemBySlug(string)?: TextWidgetItem
    * $item->caption -
    * $item->description -
    * $item->text - text (форматируемый текст)
 */
    /** @var \App\Modules\Page\Entity\Widgets\TextWidget $widget */
@endphp
<div class="container">
    @foreach($widget->items as $item)
        <div class="text-center m-b_30 notification-top">
            {!! $item->text !!}
        </div>
    @endforeach
</div>
