<!--template:Тестовая акция-->
@php
    /**
    * Banner::class - string
    * $banner->caption - string
    * $banner->description - string
    * BannerItem:class
    * $banner->items - Arraible
    * $item->image - Photo::class
    * $item->url - string
    * $item->caption - string
    * $item->description - string
    */
    /** @var \App\Modules\Page\Entity\PromotionWidget $widget */
@endphp

<div>
    @foreach($widget->promotion->products as $product)
        <div>
            <span>{{ $product->name }}</span>
        </div>
    @endforeach

</div>
