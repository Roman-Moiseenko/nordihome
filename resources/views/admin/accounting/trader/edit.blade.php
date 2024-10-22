@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование продавца
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.trader.update', $trader) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.accounting.trader._fields-form', ['trader' => $trader])
    </form>
@endsection


