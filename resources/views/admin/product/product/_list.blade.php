<x-base.table.tr class="tr-with-hidden">
    <x-base.table.td class="py-0 px-0">
        <input class="form-check-input check-products" type="checkbox" name="check[]" value="{{ $product->id }}" />
    </x-base.table.td>
    <x-base.table.td>
        <div class="image-fit w-10 h-10">
            <img class="" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="text-center">
        <span class="@if(!$product->isPublished()) italic @endif @if(!$product->isSale()) text-danger @endif">
        {{ $product->code }}
        </span>
    </x-base.table.td>
    <x-base.table.td class="td-with-hidden"><a href="{{ route('admin.product.edit', $product) }}"
                                      class="font-medium whitespace-nowrap
   @if(!$product->isPublished()) italic @endif @if(!$product->isSale()) text-danger @endif
">{{ $product->name }}</a>
        {{ ($product->isPublished()) ? '' : '(Черновик)' }}
        {{ ($product->isSale()) ? '' : '(Снят с продажи)' }}
        {{ ($product->trashed()) ? '(Удален)' : '' }}
        <div class="mt-1 fs-8 button-manage-product text-primary">
            @if($product->trashed())
                <a class="text-success fs-8" href="#"
                   onclick="event.preventDefault(); document.getElementById('form-restore-{{ $product->id }}').submit();">
                    Восстановить
                </a> |
                <a class="text-danger fs-8" href="#"
                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.full-delete', $product) }}
                >
                    Удалить окончательно
                </a>
                <form id="form-restore-{{ $product->id }}" method="post" action="{{ route('admin.product.restore', $product->id) }}">
                    @csrf
                </form>
                <form id="form-full-delete-{{ $product->id }}" method="post" action="{{ route('admin.product.full-delete', $product->id) }}">
                    @csrf
                </form>
            @else
                <a class="fs-8" href="{{ route('admin.product.edit', $product) }}">Изменить</a> |
                <a class="fs-8" href="{{ route('admin.product.show', $product) }}">Статистика</a> |
                <a class="fs-8" href="{{ ($product->isPublished()) ? route('shop.product.view', $product->slug) : route('shop.product.view-draft', $product) }}" target="_blank">
                    Просмотр
                </a> |
                <a class="text-warning fs-8" href="#"
                   onclick="event.preventDefault(); document.getElementById('form-sale-{{ $product->id }}').submit();">
                    @if($product->isSale())
                        Снять с продажи
                    @else
                        Вернуть в продажу
                    @endif
                </a> |
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
                <form id="form-sale-{{ $product->id }}" method="post" action="{{ route('admin.product.sale', $product) }}">
                    @csrf
                </form>
            @endif

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
