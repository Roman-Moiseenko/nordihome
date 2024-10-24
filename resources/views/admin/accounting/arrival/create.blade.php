@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Создание поступления
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.arrival.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.accounting.arrival._fields-form', ['arrival' => null])
    </form>

@endsection


