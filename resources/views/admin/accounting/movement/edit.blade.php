@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование перемещения
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.movement.update', $movement) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.accounting.movement._fields-form', ['movement' => $movement])
    </form>
@endsection
