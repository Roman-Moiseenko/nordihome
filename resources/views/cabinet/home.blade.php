@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Dashbord</div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
