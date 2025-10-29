<!--template:Главная слайдер вас может заинтересовать-->
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
<div id="main-slider-interesting" class="p-t_50 p-b_50">
    <div class="container">
        <h2 class="t-t_uppercase t-a_center">{{ $widget->caption }}</h2>
        <div id="slider-main-interesting" class="owl-carousel owl-theme main-slider">
            @foreach($widget->items as $item)
                <div class="row justify-content-between">
                    <div class="col-12 col-lg-6">
                        <div class="sl-text">
                            <div class="heading-border">{{ $item->caption }}</div>
                            <div class="text">{!! $item->description !!}</div>
                            <a href="{{ $item->url }}" class="btn btn-white btn-big t-t_uppercase f-z_14 m-t_20">Посмотреть</a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6" style="background: url({{ $item->getImage() }}) center center / cover no-repeat;"></div>
                </div>
            @endforeach
        </div>
    </div>
</div>
