@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $product->name }}
            </h1>
        </div>
    </div>
    <div class="font-medium text-xl text-danger mt-6">
        В разработке. После торгового учета добавится движение товара, продажи и другие отчеты
        <ol>
            <li>Показать у скольки клиентов в корзине: </li>
            <li> - для зарегистр. - колво товара общее</li>
            <li> - для незарегистр. - колво товара общее</li>
            <li>Сколько товара в резерве, у скольки клиентов</li>
            <li>Сколько в Заказах - оплаченных, но не завершенных</li>
            <li>Сколько в Заказах - в выполненных</li>
        </ol>
    </div>
@endsection
