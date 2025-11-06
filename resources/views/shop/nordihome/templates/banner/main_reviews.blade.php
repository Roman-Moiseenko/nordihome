<!--template:Главная - слайдер отзывы клиентов-->
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
<div class="main-reviews p-t_50 p-b_50" id="reviews-tab">
    <div class="container-fluid">
        <div id="main-slider-reviews" class="owl-carousel owl-theme">
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
