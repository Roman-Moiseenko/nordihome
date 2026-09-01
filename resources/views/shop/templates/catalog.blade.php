@php
    use App\Modules\Shop\Application\DTOs\CategoryTreeClientData;
    /** @var CategoryTreeClientData[] $categoryTree */
@endphp

<div class="container-xl">
    <div class="row">
        @foreach($categoryTree as $category)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                <div class="catalog-card">
                    <a href="{{ route('shop.category.view', $category->slug) }}">
                        <div>
                            <img
                                src="{{ $category->image }}"
                                alt="алт картинки">
                            <span>{{ $category->name }}</span>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
