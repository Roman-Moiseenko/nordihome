@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Платежи
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a class="btn btn-primary shadow-md mr-2" href="{{ route('admin.order.payment.create') }}">Создать платеж</a>
            {{ $payments->links('admin.components.count-paginator') }}
        </div>
    </div>
    <div class="mt-3">
        <livewire:admin.order.payment-table />
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить платеж?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
