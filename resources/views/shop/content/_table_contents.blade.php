@php
 use App\Modules\Shop\Application\DTOs\Elements\TableContent;
 /** @var TableContent[] $items */

@endphp
@if(!empty($items))
    <div class="article-chapter">
        <p><b>В этой статье:</b></p>
        <ul>
            @foreach($items as $item)
                <li><a href="#{{ $item->id }}">{{ $item->title }}</a></li>
            @endforeach
        </ul>
    </div>
@endif
