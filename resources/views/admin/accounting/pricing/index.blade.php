@extends('layouts.side-menu')

@section('subcontent')

    <h2 class="text-lg font-medium mr-auto mt-8">Установка цен</h2>

    <button class="btn btn-primary shadow-md mr-2 mt-5"
            onclick="window.location.href='{{ route('admin.accounting.pricing.create') }}'">Создать Документ
    </button>

    <div class="mt-3">
        <livewire:admin.accounting.pricing-table />
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить ценообразование?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
