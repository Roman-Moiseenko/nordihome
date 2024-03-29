@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование платежа
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.sales.payment.update', $payment) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.sales.payment._fields-form', ['payment' => $payment])
    </form>
@endsection
