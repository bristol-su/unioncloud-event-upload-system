@extends('master')

@section('title', 'UnionCloud Event Upload')

@section('content')
    <div class="links">
        <a href="{{ url('/upload') }}">Upload</a>
        <a href="{{ url('/upload/history') }}">Upload History</a>
        <a href="{{ url('/event-template') }}">Download Event Template</a>
        <a href="{{ url('/ticket-template') }}">Download Ticket Template</a>
        <a href="{{ url('/faq') }}">FAQs</a>
    </div>
@endsection('content)