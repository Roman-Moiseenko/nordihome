<!--template:Главная - слайдер специальные предложения-->
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
<div class="main-slider-sale p-t_50 p-b_50">
    <div class="container">
        <h2 class="t-t_uppercase t-a_center">{{ $banner->caption }}</h2>
        <div id="slider-payment" class="owl-carousel owl-theme main-slider">

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
