@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Новый заказ поставщику {{ $distributor->name }}
        </h2>
    </div>
    <form method="post" action="{{ route('admin.accounting.supply.store') }}">
        @csrf
        <input type="hidden" value="{{ $distributor->id }}" name="distributor">
    <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button class="btn btn-success shadow-md" type="submit" data-route="{{ route('admin.accounting.supply.store') }}">Создать ...
        </button>
    </div>
    <h3 class="mt-5 font-medium text-base">Включить в заказ товар из Стека:</h3>
    <div class="grid grid-cols-12 gap-6 mt-1">
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ОСНОВАНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">МЕНЕДЖЕР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap"></x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($stacks as $stack)
                        <x-base.table.tr>
                            <x-base.table.td class="">{{ $stack->product->name }}</x-base.table.td>
                            <x-base.table.td class="text-center">{{ $stack->quantity }}</x-base.table.td>
                            <x-base.table.td class="text-center">
                                @if(!is_null($stack->orderItem))
                                    <a href="{{ route('admin.order.show', $stack->orderItem->order) }}"

                                       class="text-success font-medium" target="_blank">{{ $stack->comment }}</a>
                                @else
                                    {{ $stack->comment . ' (' . $stack->storage->name . ')' }}
                                @endif
                            </x-base.table.td>
                            <x-base.table.td class="text-center">{{ $stack->staff->fullname->getFullName() }}</x-base.table.td>
                            <x-base.table.td class="table-report__action w-56">
                                <div class="form-check form-switch justify-center mt-3">
                                    <input id="stack-{{ $stack->id }}" class="form-check-input check-to-supply" type="checkbox" name="stack[]"
                                           value="{{ $stack->id }}" checked
                                           @if(!is_null($stack->orderItem)) readonly onclick="return false;" @endif
                                    >
                                    <label class="form-check-label" for="stack-{{ $stack->id }}"></label>
                                </div>
                            </x-base.table.td>
                        </x-base.table.tr>
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    </form>
    <script>

    </script>
@endsection
