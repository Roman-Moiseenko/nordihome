@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование данных о работнике
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.worker.update', $worker) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.worker._fields-form', ['worker' => $worker])
    </form>
@endsection
