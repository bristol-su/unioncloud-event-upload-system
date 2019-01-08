@extends('master')

@section('title', 'Upload History')

@section('content')


    <h3 style="text-align: left">Uploads:</h3>
{{--    @php dd($uploads); @endphp--}}
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Upload ID</th>
                <th>Name</th>
                <th>Date Uploaded</th>
                <th>Status (This gets stuck on uploading so ignore it Tom!)</th>
                <th>View</th>
            </tr>
            </thead>
            <tbody>
            @foreach($uploads as $upload)
                <tr>
                    <th>#{{$upload->id}}</th>
                    <th>{{$upload->name}}</th>
                    <th>{{$upload->created_at->format('d-m-Y H-i')}}</th>
                    <th>
                        @if($upload->confirmed == 0)
                            @include('status.waiting', ['text' => 'Confirm to begin upload'])
                            {{--// TODO Make this status show as complete--}}
                        @elseif($upload->uploaded == 0)
                            @if( ! empty( array_filter( $upload->events->pluck('error_message')->toArray() ) ) || ! empty( array_filter( $upload->getAllTickets()->pluck('error_message')->toArray() ) ))
                                @include('status.errored', ['text' => 'One of the events or tickets haven\'t uploaded'])
                            @else
                                @include('status.uploading', ['text' => 'Uploading'])
                            @endif
                        @else
                            @include('status.uploaded', ['text' => 'Uploaded'])
                        @endif
                    </th>
                    <th>
                        <a href="{{url('/upload/history/'.$upload->id)}}">
                            <button type="button" class="btn btn-outline-info">View</button>
                        </a>
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <br/>

@endsection('content)