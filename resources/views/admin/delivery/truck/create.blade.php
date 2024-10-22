@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Добавление транспорта
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.delivery.truck.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.delivery.truck._fields-form', ['truck' => null])
    </form>

@endsection
