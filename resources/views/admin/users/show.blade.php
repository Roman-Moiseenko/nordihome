@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h1 class="text-lg font-medium mr-auto">
            {{ $user->fullName->getFullName() }}
        </h1>
    </div>

    <div class="d-flex flex-row mb-3">
        @if ($user->isWait())
            <form method="POST" action="{{ route('admin.users.verify', $user) }}" class="mr-1">
                @csrf
                <button class="btn btn-success">Verify</button>
            </form>
        @endif

    </div>


    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th>ID</th><td>{{ $user->id }}</td>
        </tr>
        <tr>
            <th>Name</th><td>{{ $user->name }}</td>
        </tr>
        <tr>
            <th>Email</th><td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if ($user->isWait())
                    <span class="badge bg-secondary">Waiting</span>
                @endif
                @if ($user->isActive())
                    <span class="badge bg-primary">Active</span>
                @endif
            </td>
        </tr>
        <!-- role -->
        <tbody>
        </tbody>
    </table>
@endsection
