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
