<!--template:Каталог на главную страницу-->
@php
    /**
    * $widget->name
    * $widget->url
    * $widget->caption
    * $widget->description
    * $widget->products - array Products
 */

       use App\Modules\Content\Entity\Widgets\CatalogWidget;
       /** @var CatalogWidget $widget  */
@endphp

<div>
    @foreach($widget->items as $item)
        <div style="scroll-snap-align: start;max-width: 100%; overflow: hidden;text-align: left;">
            <a href="{{ $item->url() }}"
               style="max-width: 100%; overflow: hidden;">
        <img loading="lazy" src="{{ $item->image() }}"
                 alt="{{ $item->name() }}" style="width: 100%;"/>

            </a>
            <a href="{{ $item->url() }}">
                <div class="d-flex justify-content-between">
                    <div class="name">{{ $item->name() }}</div>

                </div>
                <div class="category">
                    {{ $item->name() }}
                </div>
            </a>
        </div>
    @endforeach
</div>
