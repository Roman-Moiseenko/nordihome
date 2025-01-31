<!--template:3 группы товаров слайдом-->
@php
 /**
 * $widget->banner - Banner::class
 * $widget->name
 * $widget->url
 * $widget->caption
 * $widget->description
 * $widget->items - array WidgetItem
 * $item->slug
 * $item->name
 * $item->url
 * $item->caption
 * $item->description
 * $item-group - Group::class
 */

    use App\Modules\Page\Entity\Widget;
    /** @var Widget $widget  */
@endphp
<div class="text-center mt-5 py-4 widget-home-3-group">
     <h2 class="fw-semibold mt-5">{{ $widget->caption }}</h2>
    <h3>{{ $widget->description }}</h3>
    <ul class="caption-group">
        @foreach($widget->items as $i => $item)
            <li class="{{ $i == 0 ? 'active' : '' }}">
                {{ $item->caption }}
            </li>
        @endforeach
    </ul>

</div>
