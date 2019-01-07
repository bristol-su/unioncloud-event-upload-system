@extends('master')

@section('title', 'Ticket Template')

@section('content')
    <h2>This template defines the tickets you want to upload to UnionCloud. Columns with a red star (<span style="color: red;">*</span>) are mandatory.</h2>

    @include('components.downloadbutton')

    @include('components.documentation')
@endsection('content)