@php
    use App\Modules\Shop\Application\DTOs\IkeaTreeClientData;
    /** @var IkeaTreeClientData $category */
@endphp
<div class="col-12 col-sm-6 col-md-4">
    <div class="ikea-card">

        <img src="{{ (empty($category->image)) ? '\images\no-image.jpg' : $category->image }}"/>

        <p>
            <a href="{{ route('shop.ikea.view', $category->slug) }}">
                <b>{{ $category->name }}</b>
            </a>
        </p>
        <ul>
            @foreach($category->children as $child)
                <li>
                    <a href="{{ route('shop.ikea.view', $child->slug) }}">
                        {{ $child->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
