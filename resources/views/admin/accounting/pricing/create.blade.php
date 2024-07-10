@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создание Ценообразование
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.pricing.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.accounting.pricing._fields-form', ['pricing' => null])
    </form>

@endsection


