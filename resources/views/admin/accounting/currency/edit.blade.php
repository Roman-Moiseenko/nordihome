@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование курса валюты
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.currency.update', $currency) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.accounting.currency._fields-form', ['currency' => $currency])
    </form>
@endsection
