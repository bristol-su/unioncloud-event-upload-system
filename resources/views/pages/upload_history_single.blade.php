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

    <h3 style="text-align: left">Events:</h3>

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Status</th>
                @foreach(\App\Event::getColumnArrays() as $header)
                    <th>{{ ucwords(strtolower(str_replace('_', ' ', $header))) }}</th>
                @endforeach
                <th>Tickets</th>
            </tr>
            </thead>
            <tbody>
            @foreach($events as $event)
                <tr>
                    <th>
                        {{--// TODO Make these look nicer--}}
                        @if($upload->confirmed === 0)
                            Waiting on confirmation
                        @elseif($event->uploaded === 0)
                            @if($event->error_message === null)
                                Uploading
                            @else
                                Errored: {{$event->error_message}}
                            @endif
                        @elseif(!empty(array_filter($event->tickets->pluck('error_message')->toArray())))
                            Uploaded (At least one ticket errored)
                        @else
                            Uploaded
                        @endif
                    </th>
                    @foreach(\App\Event::getColumnArrays() as $header)
                        @if($header == 'id')<th>#{{$event->$header}}</th>
                        @else <th>{{ $event->$header }}</th>
                        @endif
                    @endforeach
                    <th>
                        @foreach($event->tickets as $ticket)#{{$ticket->id}}, @endforeach
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
                    <th>
                        {{--// TODO Make these look nicer--}}
                        @if($upload->confirmed === 0)
                            Waiting on confirmation
                        @elseif($event->uploaded === 0)
                            @if($event->error_message === null)
                                Uploading
                            @else
                                Errored: {{$event->error_message}}
                            @endif
                        @else
                            Uploaded
                        @endif
                    </th>
    <br/>
    {{--// TODO Allow event csvs to be downloaded selectively. You should be able to check the events you want to download, which will produce a ticket and csv spreadsheet--}}
    <h3 style="text-align: left">Tickets:</h3>

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Status</th>
                @foreach(\App\Ticket::getColumnArrays() as $header)
                    <th>{{ ucwords(strtolower(str_replace('_', ' ', $header))) }}</th>
                @endforeach
                <th>Events</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <th>
                        {{--// TODO Make these look nicer--}}
                        @if($upload->confirmed === 0)
                            Waiting on confirmation
                        @elseif($ticket->uploaded === 0)
                            @if($ticket->error_message === null)
                                @if($ticket->event->error_message !== null)
                                    Event couldn't be uploaded
                                @else
                                    Uploading
                                @endif
                            @else
                                Errored
                            @endif
                        @else
                            Uploaded
                        @endif
                    </th>
                    @foreach(\App\Ticket::getColumnArrays() as $header)
                        @if($header == 'id')<th>#{{$ticket->$header}}</th>
                        @else <th>{{ $ticket->$header }}</th>
                        @endif
                    @endforeach
                    <th>
                        #{{$ticket->event()->get()->pluck('id')->first()}}
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection('content)