<h4>
    <ul style="text-align: left;">
        @foreach($columns as $column)
            <li><span style="text-decoration: underline;">{{$column['name']}}</span> @if($column['mandatory'])<span style="color: red;">*</span>@endif
                <ul>
                    <li>{!! $column['description'] !!}</li>
                    <li>Examples:
                        <ul>
                            @foreach($column['examples'] as $example)
                                <li>{{$example}}</li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </li>
            <hr style="width: 50%"/>
        @endforeach
    </ul>
</h4>