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
    /** @var \App\Modules\Discount\Entity\Promotion $promotion */
@endphp

<div>
    @foreach($promotion->products as $product)
        <div>
            <span>{{ $product->name }}</span>
        </div>
    @endforeach

</div>
