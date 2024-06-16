<x-base.table.tr>
    <x-base.table.td>
        <div class="image-fit w-10 h-10">
            <img class="" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $product->code }}</x-base.table.td>
    <x-base.table.td class="td-with-hidden"><a href="{{ route('admin.product.edit', $product) }}"
                                      class="font-medium whitespace-nowrap">{{ $product->name }}</a> {{ ($product->published) ? '' : '(Черновик)' }}
        <div class="mt-1 fs-8 button-manage-product text-primary">
            <a class="fs-8" href="{{ route('admin.product.edit', $product) }}">Изменить</a> |
            <a class="fs-8" href="{{ route('admin.product.show', $product) }}">Статистика</a> |
            <a class="fs-8" href="{{ route('shop.product.view', $product->slug) }}" target="_blank">Просмотр</a> |
            <a class="text-success fs-8" href="#"
               onclick="event.preventDefault(); document.getElementById('form-toggle-{{ $product->id }}').submit();">
                @if($product->isPublished())
                    В черновик
                @else
                    Опубликовать
                @endif
            </a> |
            <a class="text-danger fs-8" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.destroy', $product) }}
            >Удалить</a>
            <form id="form-toggle-{{ $product->id }}" method="post" action="{{ route('admin.product.toggle', $product) }}">
                @csrf
            </form>
        </div>
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $product->category->name }}</x-base.table.td>
    <x-base.table.td class="text-center whitespace-nowrap">{{ price($product->getPriceRetail()) }}
        @if($product->hasPromotion())
            <div class="text-danger font-medium">
                {{ price($product->promotion()->pivot->price) }}
            </div>
        @endif
    </x-base.table.td>
    <x-base.table.td class="text-right">
        <span class="font-medium">{{ $product->getQuantity() }}</span> /
        <span class="text-success">{{ $product->getCountSell() }}</span> /
        <span class="text-danger">{{ $product->getReserveCount() }}</span>
    </x-base.table.td>
</x-base.table.tr>
