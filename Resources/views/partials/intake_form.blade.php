@foreach($intake as $value)
    <div class="form-group">
        <label class="control-label col-md-4">{{ $value['label'] }}</label>
        
        @if($value['type'] == 'text')

        @elseif($value['type'] == 'select')
            <select class="form-control">
                @foreach($value['values'] as $option)
                    <option>{{ $option->name }}</option>
                @endforeach
            </select>
        @elseif($value['type'] == 'radio')
            <
        @endif
        {{-- {{  dump($key.label) }} --}}
    </div>
@endforeach
