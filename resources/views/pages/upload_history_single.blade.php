@extends('master')

@php
    if($upload->confirmed === 0){
        $confirmation = true;
    } else {
        $confirmation = false;
    }
@endphp

@section('title') @if($confirmation) Confirm your Upload @else Upload History #{{$upload->id}}@endif @endsection

@section('content')
    @if($confirmation)
        <h2>Check your upload here. If there are any issues, click cancel to go back. A draft of your upload will be saved.</h2>

        <hr style="width: 50%" />
        <br/>

        <form action="{{ url('/upload/confirm') }}" method="POST">
            @csrf
            <input type="hidden" name="upload" value="{{$upload->id}}"/>

            <div class="form-group">
                <div class="col-md-12">
                    <button id="upload-csv" name="upload-csv" class="btn btn-success" style="width: 100%">Confirm and Upload</button>
                </div>
            </div>

        </form>
        <form action="{{ url('/') }}" method="GET">
            @csrf
            <div class="form-group">
                <div class="col-md-12">
                    <button id="back" name="back" class="btn btn-danger" style="width: 100%">Cancel</button>
                </div>
            </div>

        </form>
    @else
        <h2>Upload '{{$upload->name}}':</h2>

        <hr style="width: 50%" />
        <br/>
    @endif


    <!--Event Table -->

    <h3 style="text-align: left">Events:</h3>

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th style="border-top: none;">Event ID</th>
                <th style="border-top: none;">Status</th>
                <th style="border-top: none;">Tickets</th>
                <th style="border-top: none;">Actions</th>
            @foreach(\App\Event::getColumnArrays() as $header)
                    <th style="border-top: none;">{{ ucwords(strtolower(str_replace('_', ' ', $header))) }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($events as $event)
                <tr>
                    <th>#{{$event->id}}</th>
                    {{--Status--}}
                    <th>
                        @if($upload->confirmed === 0)
                            @include('status.waiting', ['text'=>'Waiting for confirmation'])
                        @elseif($event->uploaded === false)
                            @if($event->error_message === null)
                                @include('status.uploading', ['text'=>'Uploading'])
                            @else
                                @include('status.errored', ['text'=>$event->error_message])
                            @endif
                        @elseif(!empty(array_filter($event->tickets->pluck('error_message')->toArray())))
                            @include('status.uploaded', ['text'=>'At least one ticket failed'])
                        @else
                            @include('status.uploaded', ['text'=>'Uploaded'])
                        @endif
                    </th>
                    <th>
                        @foreach($event->tickets as $ticket)#{{$ticket->id}}, @endforeach
                    </th>
                    <th style="border-right: 2px solid #dee2e6;">
                        @if($event->uploaded === false && $event->error_message !== null)
                            <form action="{{url('upload/event/retry/'.$event->id)}}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-warning">Retry</button>
                            </form>
                        @endif
                    </th>
                    @foreach(\App\Event::getColumnArrays() as $header)
                        <th>{!! (is_array($event->$header)?implode(', ', array_map(function($h) { return $h['id']; }, $event->$header)):(is_bool($event->$header)?'<i class="fa fa-'.($event->$header?'check':'times').'""></i>':$event->$header)) !!}</th>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <br/>
    {{--// TODO Allow event csvs to be downloaded selectively. You should be able to check the events you want to download, which will produce a ticket and csv spreadsheet--}}
    {{--// TODO add 'retry' action button on each event, which just adds the event as a task. Add a delete/restore button, and a permanently delete button too--}}



    {{--Ticket Table--}}

    <h3 style="text-align: left">Tickets:</h3>

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th style="border-top: none;">Ticket ID</th>
                <th style="border-top: none;">Status</th>
                <th style="border-top: none;">Events</th>
                <th>Action</th>
                @foreach(\App\Ticket::getColumnArrays() as $header)
                    <th style="border-top: none;">{{ ucwords(strtolower(str_replace('_', ' ', $header))) }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <th>#{{$ticket->id}}</th>
                    <th>
                    @if($upload->confirmed === 0)
                            @include('status.waiting', ['text'=>'Waiting for confirmation'])
                        @elseif($ticket->uploaded === false)
                        @if($ticket->error_message === null)
                            @if($ticket->event->error_message !== null)
                                @include('status.waiting', ['text'=>'Event couldn\'t be uploaded'])
                            @else
                                @include('status.uploading', ['text'=>'Uploading'])
                            @endif
                        @else
                            @include('status.errored', ['text'=>$ticket->error_message])
                        @endif
                    @else
                        @include('status.uploaded', ['text'=>'Uploaded'])
                    @endif
                    </th>
                    <th>#{{$ticket->event()->get()->pluck('id')->first()}}</th>
                    <th style="border-right: 2px solid #dee2e6;">
                        @if($ticket->uploaded === false && $ticket->error_message !== null)
                            <form action="{{url('upload/ticket/retry/'.$ticket->id)}}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-warning">Retry</button>
                            </form>
                        @endif
                    </th>
                    @foreach(\App\Ticket::getColumnArrays() as $header)
                        <th style="white-space: nowrap;">{!! (is_array($ticket->$header)?implode(', ', array_map(function($h) { return $h['id']; }, $ticket->$header)):(is_bool($ticket->$header)?'<i class="fa fa-'.($ticket->$header?'check':'times').'""></i>':$ticket->$header)) !!}</th>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection('content)