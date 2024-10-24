@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование поступления
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.arrival.update', $arrival) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.accounting.arrival._fields-form', ['arrival' => $arrival])
    </form>
@endsection
