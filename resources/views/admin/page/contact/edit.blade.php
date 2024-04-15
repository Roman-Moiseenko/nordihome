@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать контакт
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.page.contact.update', $contact) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        @include('admin.page.contact._fields-form', ['contact' => $contact])
    </form>

@endsection
