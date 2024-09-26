@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавление продавца
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.trader.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.accounting.trader._fields-form', ['trader' => null])

        <x-company.fields />
    </form>
@endsection
