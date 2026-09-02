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

<div class="row">
    @foreach($widget->items as $item)
        <div class="col-6 col-sm-6 col-md-4 col-lg-3">
            <div class="catalog-card">
                <a href="{{ $item->url() }}">
                    <div>
                        <img
                            src="{{ $item->image() }}"
                            alt={{ $item->image() }}>
                        <span>{{ $item->name() }}</span>
                    </div>
                </a>
            </div>
        </div>
    @endforeach
</div>
