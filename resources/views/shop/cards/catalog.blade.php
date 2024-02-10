<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="catalog-card">
        <a href="{{ route('shop.category.view', $category->slug) }}">
            <div style="position: relative">
                <img
                    src="{{ (empty($category->image->file)) ? '\images\no-image.jpg' : $category->image->getTHumbUrl('catalog') }}">
                <span>{{ $category->name }}</span>
            </div>
        </a>
    </div>
</div>
