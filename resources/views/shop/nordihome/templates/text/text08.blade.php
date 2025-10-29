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
<div class="block-faq p-t_50 p-b_50 bg-black" id="faq-tab">
    <div class="container">
        <h2 class="t-t_uppercase t-a_center">{{ $widget->caption }}</h2>
        <div class="accordion accordion_faq" id="faq-tab">
            @foreach($widget->items as $item)
                <div class="accordion-item">
                    <div class="accordion-header" id="panelsStayOpen-heading{{$item->slug}}">
                        <div class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse{{$item->slug}}" aria-expanded="false" aria-controls="panelsStayOpen-collapse{{$item->slug}}">
                            {{ $item->caption }}
                        </div>
                    </div>
                    <div id="panelsStayOpen-collapse{{$item->slug}}" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading{{$item->slug}}">
                        <div class="accordion-body">
                            {!! $item->text !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

