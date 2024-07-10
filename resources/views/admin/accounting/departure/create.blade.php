@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создание списания
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.departure.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.accounting.departure._fields-form', ['departure' => null])
    </form>

@endsection


