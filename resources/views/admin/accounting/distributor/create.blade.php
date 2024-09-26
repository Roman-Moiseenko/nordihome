@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавление поставщика
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.distributor.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.accounting.distributor._fields-form', ['distributor' => null])

        <x-company.fields />
    </form>
@endsection
