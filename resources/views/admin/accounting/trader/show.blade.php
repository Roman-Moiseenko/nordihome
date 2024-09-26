@extends('layouts.side-menu')

@section('subcontent')

    <x-company.info
        :title="$trader->name"
        :company="$trader->organization"
        route="{{ route('admin.accounting.organization.edit', $trader->organization) }}"
    />


@endsection
