@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Создание перемещения
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.movement.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.accounting.movement._fields-form', ['movement' => null])
    </form>

@endsection


