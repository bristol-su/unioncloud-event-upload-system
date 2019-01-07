@extends('master')

@section('title', 'Upload')

@section('content')
    {{--{{dd($errors)}}--}}
    @if($errors->has('validation-errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->get('validation-errors') as $error)
                    <li style="text-align: left;">{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {!! $errors->first('validation-error', '<div class="alert alert-danger">:message</div>') !!}
    <form action="{{ url('/upload') }}" method="POST"  enctype="multipart/form-data">
        @csrf

        <span class="help-text">Give this upload a name so you can find it later.</span>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroupFileAddon01">Upload Name</span>
            </div>
            <input id="upload-name" name="upload-name" type="text" value="fit-and-fab-{{\Carbon\Carbon::now()->format('d_m_Y-H-i')}}" class="form-control" aria-describedby="inputGroupFileAddon01">
        </div>
        {!! $errors->first('event-spreadsheet', '<p class="invalid-feedback" style="display: block;">:message</p>') !!}

        <!-- CSV Event Sheet -->
        <span class="help-text">Find the event template <a href="{{url('/event-template')}}">here</a></span>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroupFileAddon01">Events</span>
            </div>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputGroupFile01" name="event-spreadsheet" aria-describedby="inputGroupFileAddon01">
                <label class="custom-file-label" for="inputGroupFile01">Choose the Event CSV</label>
            </div>
        </div>
        {!! $errors->first('event-spreadsheet', '<p class="invalid-feedback" style="display: block;">:message</p>') !!}

        <!-- CSV Ticket Sheet -->
        <span class="help-text">Find the ticket template <a href="{{url('/ticket-template')}}">here</a></span>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroupFileAddon02">Tickets</span>
            </div>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputGroupFile02" name="ticket-spreadsheet" aria-describedby="inputGroupFileAddon02">
                <label class="custom-file-label" for="inputGroupFile02">Choose the Ticket CSV</label>
            </div>
        </div>
        {!! $errors->first('ticket-spreadsheet', '<p class="invalid-feedback" style="display: block;">:message</p>') !!}

        <div class="form-group">
            <div class="col-md-12">
                <button id="upload-csv" name="upload-csv" class="btn btn-success" style="width: 100%">Next</button>
            </div>
        </div>

    </form>

    <script>
        $('#inputGroupFile01, #inputGroupFile02').on('change',function(){
            console.log('Should have changed')
            var fileName = $(this).val();
            $(this).next('.custom-file-label').html(fileName);
        })
    </script>
@endsection('content)