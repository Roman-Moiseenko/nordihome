@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование списания
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.departure.update', $departure) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.accounting.departure._fields-form', ['departure' => $departure])
    </form>
@endsection
