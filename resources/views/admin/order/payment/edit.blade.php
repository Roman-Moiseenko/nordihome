@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование платежа заказа {{ $payment->order->htmlNum() }}
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.order.payment.update', $payment) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.order.payment._fields-form', ['payment' => $payment])
    </form>
@endsection
