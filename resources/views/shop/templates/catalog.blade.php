@php
    use App\Modules\Shop\Application\DTOs\CategoryTreeClientData;
    /** @var CategoryTreeClientData[] $categoryTree */
@endphp
<div>
    Здесь Будет КАТАЛОГ
    @foreach($categoryTree as $category)
        <p>
            {{ $category->name }}
        </p>
    @endforeach
</div>
<div class="row">
    <div class="col-6 col-sm-6 col-md-4 col-lg-3">
        <div class="catalog-card">
            <a href="/">
                <div>
                    <img
                        src="/"
                        alt="алт картинки">
                    <span>Название категории</span>
                </div>
            </a>
        </div>
    </div>
</div>
