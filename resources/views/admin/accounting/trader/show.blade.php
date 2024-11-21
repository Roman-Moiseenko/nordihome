@extends('layouts.side-menu')

@section('subcontent')

    <x-company.info
        :title="$trader->name"
        :company="$trader->organization"
        route="{{ route('admin.accounting.organization.show', $trader->organization) }}"
    />


@endsection
