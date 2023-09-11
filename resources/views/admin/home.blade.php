@extends('layouts.app')

@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home') }}">Dashboard</a></li>
        @can('user-manager')
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
        @endcan
    </ul>
@endsection
