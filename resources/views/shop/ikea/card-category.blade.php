@php
    use App\Modules\Shop\Application\DTOs\IkeaTreeClientData;
    /** @var IkeaTreeClientData $category */
@endphp
<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="ikea-card m-b_20">

        <img src="{{ (empty($category->image)) ? '\images\no-image.jpg' : $category->image }}" class="b-radius_12"/>

        <p>
            <a href="{{ route('shop.ikea.view', $category->slug) }}">
                <b>{{ $category->name }}</b>
            </a>
        </p>
        <ul>
            @foreach($category->children as $child)
                <li>
                    <a href="{{ route('shop.ikea.view', $child->slug) }}" class="f-z_16">
                        {{ $child->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
