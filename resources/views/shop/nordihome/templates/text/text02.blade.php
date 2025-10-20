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
    /** @var \App\Modules\Page\Entity\TextWidget $widget */
@endphp
<div>
    @foreach($widget->items as $item)
        <div class="text-center mb-5 notification-top">
            {!! $item->text !!}
        </div>
    @endforeach
</div>
