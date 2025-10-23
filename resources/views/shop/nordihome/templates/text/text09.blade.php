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
<div class="main-catalog-mini">
    <div class="">
        <a href="/shop/" class="c-mini-item">
            <img src="/wp-content/uploads/2023/04/t-nalichie-1-min.jpg" alt="Товары Икеа в наличии">
            <div class="heading">Каталог товаров</div>
        </a>
        <a href="/calculate/" class="c-mini-item">
            <img src="/wp-content/uploads/2023/04/t-zakaz-1-min.jpg" alt="Товары Икеа под заказ">
            <div class="heading">Весь ассортимент под заказ</div>
        </a>
    </div>
</div>
