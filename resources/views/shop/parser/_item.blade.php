<div class="parser-item box-card">
    <div class="image">
        <img src="{{ $item->product->getImage('thumb') }}" alt="{{ $item->product->name }}">
    </div>
    <div class="data">
        <h4>{{ $item->product->name }}</h4>
        <div class="description-product">{{ $item->product->short }}</div>
        <div><span>Артикул: </span><span class="code-selected">{{ $item->product->code }}</span></div>
        <div><span>Вес: </span><strong>{{ $item->product->weight() }} кг</strong></div>
        <div><span>Кол-во пачек: </span><strong>{{ $item->parser->packs }} шт.</strong></div>
        <div><span>Наличие в ИКЕА: </span></div>
        <div class="quantity">{!! $item->storages !!}</div>
        <div class="bottom">
            <div class="cost">{{ price($item->cost) }}</div>
            <div class="form">
                <button id="delete-button" class="btn btn-outline-dark"
                        onclick="event.preventDefault(); document.getElementById('form-remove-{{$i}}').submit();">
                    <i class="fa-light fa-trash-can"></i>
                </button>
                <form id="form-remove-{{$i}}" method="post" action="{{ route('shop.parser.remove', $item->product) }}">
                    @csrf
                </form>
                <button class="decrease-button btn btn-outline-dark"  data-code="{{ $item->product->id }}"><i class="fa-light fa-minus"></i></button>
                <input id="count-{{ $item->product->id }}" type="text" class="form-control parser-set-input" autocomplete="off"
                       data-product="{{ $item->product->id }}" value="{{ $item->quantity }}"/>
                <button class="increase-button btn btn-outline-dark" data-code="{{ $item->product->id }}"><i class="fa-light fa-plus"></i></button>
            </div>
        </div>
    </div>
</div>
