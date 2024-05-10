@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование данных о транспорте
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.delivery.truck.update', $truck) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.delivery.truck._fields-form', ['truck' => $truck])
    </form>
@endsection
