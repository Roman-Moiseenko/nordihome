@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать группу {{ $group->name }}
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.group.update', $group) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.product.group._fields-form', ['group' => $group])
    </form>
@endsection
