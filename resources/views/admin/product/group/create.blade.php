@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Создать группу
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.group.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.product.group._fields-form', ['group' => null])
    </form>
@endsection
