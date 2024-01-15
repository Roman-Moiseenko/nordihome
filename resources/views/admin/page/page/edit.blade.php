@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать страницу
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.page.page.update', $page) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        @include('admin.page.page._fields-form', ['page' => $page])
    </form>

@endsection
