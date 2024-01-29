<div class="parser-list-item box-card" style="">
    <div class="parser-item-img">
        <img src="{{ $item->product->photo->getThumbUrl('thumb') }}" alt="{{ $item->product->name }}">
    </div>
    <div class="parser-item-data">
        <h4>{{ $item->product->name }}</h4>
        <div class="description-product">{{ $item->product->short }}</div>
        <div><span>Артикул: </span><span class="code-selected">{{ $item->product->code }}</span></div>
        <div><span>Вес: </span><strong>{{ $item->product->dimensions->weight }} кг</strong></div>
        <div><span>Кол-во пачек: </span><strong>{{ $item->parser->packs }} шт.</strong></div>
        <div><span>Наличие в ИКЕА: </span></div>
        <div class="parser-item-quantity">{!! $item->storages !!}</div>
        <div class="parser-list-item--bottom">
            <div class="parser-list-item--cost">{{ price($item->cost * $item->quantity) }}</div>
            <div class="parser-list-item--form">
                <button id="delete-button" class="btn btn-outline-dark"
                        onclick="event.preventDefault(); document.getElementById('form-remove-{{$i}}').submit();">
                    <i class="fa-light fa-trash"></i>
                </button>
                <form id="form-remove-{{$i}}" method="post" action="{{ route('shop.parser.remove', $item->product) }}">
                    @csrf
                </form>
                <button class="decrease-button btn btn-outline-dark"  data-code="{{ $item->product->id }}"><i class="fa-light fa-minus"></i></button>
                <div><div id="count-{{ $item->product->id }}">{{ $item->quantity }}</div></div>
                <button class="increase-button btn btn-outline-dark" data-code="{{ $item->product->id }}"><i class="fa-light fa-plus"></i></button>
            </div>
        </div>
    </div>
</div>
