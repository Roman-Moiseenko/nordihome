@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование поставщика
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.distributor.update', $distributor) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.accounting.distributor._fields-form', ['distributor' => $distributor])
    </form>
@endsection


