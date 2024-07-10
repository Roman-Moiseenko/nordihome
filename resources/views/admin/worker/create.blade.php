@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавление работника
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.worker.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.worker._fields-form', ['worker' => null])
    </form>

@endsection
