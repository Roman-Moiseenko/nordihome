<!--template:Главная - часто задаваемые вопросы-->
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
<div class="block-faq p-t_50 p-b_50 bg-black" id="faq-tab">
    <h2 class="t-t_uppercase t-a_center">{{ $widget->caption }}</h2>
    <div class="accordion_1">
        @foreach($widget->items as $item)
            <div class="accordion-item">
                <div class="accordion-heading">{{ $item->caption }}</div>
                <div class="accordion-text">
                    <p>{!! $item->text !!}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
