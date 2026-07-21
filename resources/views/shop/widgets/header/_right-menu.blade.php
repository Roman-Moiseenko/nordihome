@php
    use App\Modules\Shop\Application\DTOs\CategoryTreeClientData;
    use App\Modules\Shop\Application\DTOs\RoomTreeClientData;

    /** @var CategoryTreeClientData[]|RoomTreeClientData[] $categories */
    /** @var string $entity - category или room    */
@endphp

@foreach($categories as $category)
    <div class="tab-pane fade" id="{{ $category->slug }}" role="tabpanel">
        <div class="f-w_600 f-z_21 m-b_30">{{ $category->name }}</div>
        <div class="row">
            @foreach($category->children as $categorySecond)
                <div class="col-md-4">
                    <div class="submenu-links">
                        <div class="f-w_600 m-b_20">{{ $categorySecond->name }}</div>
                        <ul class="m-b_20">
                            @foreach($categorySecond->children as $categoryThird)
                                <li><a href="{{ route('shop.category.view', $categoryThird->slug) }}">{{ $categoryThird->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
