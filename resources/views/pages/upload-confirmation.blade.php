@extends('master')

@section('title', 'Confirm your Upload')

@section('content')


    <h3 style="text-align: left">Events:</h3>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    @foreach(\App\Event::getColumnArrays() as $header)
                        <th>{{ ucwords(strtolower(str_replace('_', ' ', $header))) }}</th>
                    @endforeach
                    <th>Tickets</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr>
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

    <br/>

    <h3 style="text-align: left">Tickets:</h3>

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                @foreach(\App\Ticket::getColumnArrays() as $header)
                    <th>{{ ucwords(strtolower(str_replace('_', ' ', $header))) }}</th>
                @endforeach
                <th>Events</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tickets as $ticket)
                <tr>
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