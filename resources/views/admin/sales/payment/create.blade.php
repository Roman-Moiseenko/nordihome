@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создание платежа
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.sales.payment.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.sales.payment._fields-form', ['payment' => null])
    </form>

@endsection


