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
                <th>Status</th>
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
                            {{-- // TODO Style the statuses nicely --}}
                            Not Confirmed
                        @elseif($upload->uploaded == 0)
                           Uploading
                        @else
                            Uploaded
                        @endif
                    </th>
                    <th>
                        {{-- // TODO Style nicely --}}
                        <a href="{{url('/upload/history/'.$upload->id)}}">View</a>
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <br/>

@endsection('content)