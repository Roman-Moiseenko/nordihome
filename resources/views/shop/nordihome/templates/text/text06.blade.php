<!--template:Главная - блок каталог товаров и весь ассортимент-->
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
<div class="main-catalog-mini">
    <div class="row">
        <div class="col-lg-6">
            <a href="/shop/" class="c-mini-item">
                <img src="/3b907309-9689-42f7-af88-109c9303e2c1.jpg" alt="Товары Икеа в наличии">
                <div class="heading">Каталог товаров</div>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="/calculate/" class="c-mini-item">
                <img src="/8ad41390-77e9-4b65-be52-c2e5b2d86ec1" alt="Товары Икеа под заказ">
                <div class="heading">Весь ассортимент под заказ</div>
            </a>
        </div>
    </div>
</div>
