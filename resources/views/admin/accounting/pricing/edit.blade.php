@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование Ценообразования
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.pricing.update', $pricing) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.accounting.pricing._fields-form', ['pricing' => $pricing])
    </form>
@endsection
