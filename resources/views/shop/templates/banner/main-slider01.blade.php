<!--template:Главная - первый слайдер-->
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
    /** @var \App\Modules\Content\Entity\Widgets\BannerWidget $widget */
@endphp
<div class="main-specials">
    <div class="container">
        <div id="main-slider01" class="owl-carousel owl-theme">
            @foreach($widget->items as $item)
                <div>
                    <a href="{{ $item->url }}"
                       data-analytics-action="banner_click"
                       data-entity-type="banner"
                       data-entity-id="{{ $item->id }}"
                       data-analytics-payload='{
                       "widget_id": {{ $widget->id }},
                       "placement": "slider-payment",
                       "slide_index": {{ $loop->index }},
                       "title": @json($item->title ?? ''),
                       "url": @json($item->url)
                   }'>
                        <img src="{{ $item->getImage() }}"/>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
