@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Холдинги
        </h2>
    </div>

    @foreach($holdings as $holding)
        <div class="box mt-3 p-3">
            <h1 class="text-lg font-medium">{{ $holding->name }}</h1>
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ОРГАНИЗАЦИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ИНН</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">РУКОВОДИТЕЛЬ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($holding->organizations as $organization)
                        <x-base.table.tr>
                            <x-base.table.td class="">
                                <a href="{{ route('admin.accounting.organization.show', $organization) }}"
                                   class="font-medium whitespace-nowrap">{{ $organization->short_name }}</a>
                            </x-base.table.td>

                            <x-base.table.td class="text-center">{{ $organization->inn }}</x-base.table.td>

                            <x-base.table.td class="text-center">{{ $organization->chief->getShortName() }}</x-base.table.td>

                            <x-base.table.td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center text-danger" href="#"
                                       onclick="event.preventDefault(); document.getElementById('holding-detach-{{ $organization->id }}').submit();"
                                    >
                                        <x-base.lucide icon="trash-2" class="w-4 h-4"/>Detach
                                    </a>
                                    <form id="holding-detach-{{ $organization->id }}" method="POST"
                                          action="{{ route('admin.accounting.organization.holding-detach', $organization) }}">
                                        @csrf
                                    </form>
                                </div>
                            </x-base.table.td>
                        </x-base.table.tr>
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    @endforeach
@endsection
