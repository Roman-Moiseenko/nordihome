@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавление курса валюты
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.currency.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.accounting.currency._fields-form', ['currency' => null])
    </form>

@endsection


