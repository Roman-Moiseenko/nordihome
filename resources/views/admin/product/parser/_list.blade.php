<x-base.table.tr class="tr-with-hidden">
    <x-base.table.td class="">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $parser->product->getImage() }}" alt="{{ $parser->product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $parser->product->code }}</x-base.table.td>
    <x-base.table.td class="">{{ $parser->product->name . ' ' . (($parser->product->published) ? '' : '(Черновик)') }}
        <div class="mt-1 fs-8 button-manage-product text-primary">
            <a class="text-success fs-8" href="#"
               onclick="event.preventDefault(); document.getElementById('form-fragile-{{ $parser->id }}').submit();">
                @if($parser->isFragile())
                    Не хрупкий
                @else
                    Хрупкий
                @endif
            </a> |
            <a class="text-success fs-8" href="#"
               onclick="event.preventDefault(); document.getElementById('form-sanctioned-{{ $parser->id }}').submit();">
                @if($parser->isSanctioned())
                    Не санкционный
                @else
                    Санкционный
                @endif
            </a> |

            <a class="fs-8" href="{{ route('admin.product.edit', $parser->product) }}" target="_blank">
                К товару
            </a> |
            <a class="text-success fs-8" href="#"
               onclick="event.preventDefault(); document.getElementById('form-block-{{ $parser->id }}').submit();">
                @if($parser->isBlock())
                    Разблокировать
                @else
                    Заблокировать
                @endif
            </a>


            <form id="form-fragile-{{ $parser->id }}" method="post" action="{{ route('admin.product.parser.fragile', $parser) }}">
                @csrf
            </form>
            <form id="form-sanctioned-{{ $parser->id }}" method="post" action="{{ route('admin.product.parser.sanctioned', $parser) }}">
                @csrf
            </form>
            <form id="form-block-{{ $parser->id }}" method="post" action="{{ route('admin.product.parser.block', $parser) }}">
                @csrf
            </form>
        </div>
    </x-base.table.td>


    <x-base.table.td class="text-center">{{ $parser->product->category->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $parser->packs }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $parser->price }}</x-base.table.td>
    <x-base.table.td class="text-center"><x-yesNo status="{{ $parser->isFragile() }}" lucide="" class="justify-center"/></x-base.table.td>
    <x-base.table.td class="text-center"><x-yesNo status="{{ $parser->isSanctioned() }}" lucide="" class="justify-center"/></x-base.table.td>
    <x-base.table.td class="text-center"><x-yesNo status="{{ !$parser->isBlock() }}" lucide="" class="justify-center"/></x-base.table.td>

    <x-base.table.td class="text-center"> {!! $parser->composite() !!} </x-base.table.td>

</x-base.table.tr>
