<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $parser->product->getImage() }}" alt="{{ $parser->product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.edit', $parser->product) }}"
                                      class="font-medium whitespace-nowrap">{{ $parser->product->name }}</a> {{ ($parser->product->published) ? '' : '(Черновик)' }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $parser->product->category->name }}</x-base.table.td>
    <x-base.table.td class="w-40 text-center">{{ $parser->packs }}</x-base.table.td>
    <x-base.table.td class="w-40 text-center">{{ $parser->price }}</x-base.table.td>
    <x-base.table.td class="w-40 text-center"><x-yesNo status="{{ $parser->order }}" lucide="" class="justify-center"/></x-base.table.td>
    <x-base.table.td class="text-center"> {!! $parser->composite() !!} </x-base.table.td>
    <x-base.table.td class="text-center"> {!! $parser->quantity() !!} </x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($parser->order)
            <a class="flex items-center mr-3" href="{{ route('admin.product.parser.block', $parser) }}"
               onclick="event.preventDefault(); document.getElementById('form-parser-block-{{ $parser->id }}').submit();"
            >
                <x-base.lucide icon="search-x" class="w-4 h-4"/>
                Block </a>
                <form id="form-parser-block-{{ $parser->id }}" method="post" action="{{ route('admin.product.parser.block', $parser) }}">
                    @csrf
                </form>
            @else
                <a class="flex items-center mr-3" href="{{ route('admin.product.parser.unblock', $parser) }}"
                   onclick="event.preventDefault(); document.getElementById('form-parser-unblock-{{ $parser->id }}').submit();"
                >
                    <x-base.lucide icon="search-check" class="w-4 h-4"/>
                    UnBlock </a>
                <form id="form-parser-unblock-{{ $parser->id }}" method="post" action="{{ route('admin.product.parser.unblock', $parser) }}">
                    @csrf
                </form>

            @endif

        </div>
    </x-base.table.td>
</x-base.table.tr>
