@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                Распоряжение от {{ $expense->created_at->format('d-M-Y') . ' для заказа ' . $expense->order->htmlNum() }}
            </h1>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 pb-20">
        Список товаров и услуг
    </div>

    <div class="grid grid-cols-12 gap-x-6 pb-20">
        Перемещения
    </div>

    <div class="grid grid-cols-12 gap-x-6 pb-20">
        Действия
    </div>
@endsection
