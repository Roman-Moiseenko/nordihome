<!--template:Первый слайдер на главной-->
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
    /** @var \App\Modules\Page\Entity\BannerWidget $widget */
@endphp
<div class="main-specials">
    <div class="container">
        <div id="slider-payment" class="owl-carousel owl-theme">
            @foreach($widget->items as $item)
                <div>
                    <a href="{{ $item->url }}">
                        <img src="{{ $item->getImage() }}"/>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
