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
<div class="container-xl">

    <div class="row">
        @foreach($widget->items as $item)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                <div class="catalog-card">
                    <a href="{{ $item->url() }}">
                        <div>
                            <img
                                src="{{ App\Modules\Shared\Application\Actions\GetPhotoStatic::get($item->model_type, $item->model_id, 'catalog-free') }}"
                                alt={{ $item->name() }}>
                            <span>{{ $item->name() }}</span>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="t-a_center"><a href="/catalog/" class="btn btn-black">Посмотреть весь каталог</a></div>
</div>
