<!--template:Главная - контакты с картой-->
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
    <h2>{{ $widget->caption }}</h2>
    @foreach($widget->items as $item)

        <h3>{{ $item->caption }}</h3>
        <div>
            {!! $item->text !!}
        </div>
    @endforeach
</div>
