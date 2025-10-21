<!--template:Главная - Подробности доставки в другие города-->
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
<div class="p-t_50 p-b_50 bg-blue f-z_23 t-a_center t-t_uppercase f-w_600">
    @foreach($widget->items as $item)
        <div class="text-center m-b_30 notification-top">
            {!! $item->text !!}
        </div>
    @endforeach
</div>
