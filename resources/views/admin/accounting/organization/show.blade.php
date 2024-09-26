@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $organization->full_name }}
            </h1>
        </div>
    </div>
    <x-company.info :company="$organization" route="{{ route('admin.accounting.organization.edit', $organization) }}" />
@endsection
