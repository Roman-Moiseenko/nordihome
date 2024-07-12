@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Товар в корзине
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО В КОРЗИНЕ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($products as $product)
                        @include('admin.user.cart._list', ['product' => $product])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $products->links('admin.components.paginator', ['pagination' => $pagination]) }}
@endsection
