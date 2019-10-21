@foreach($intake as $field)
    @if($field['type'] == 'text')
        <div class="row">
            <div class="col-md-12">
                {!! Former::text($field['name'])->label($field['label'])->value($field['value']) !!}
            </div>
        </div>
    @elseif($field['type'] == 'select')
        <div class="row">
            <div class="col-md-12">
                {!! Former::select($field['name'])
                    ->options($field['values'])
                    ->label($field['label'])
                    ->value($field['value']) !!}
            </div>
        </div>
    @elseif($field['type'] == 'radio')
        <div class="row">
            <div class="col-md-12">
                {!! Former::radios($field['name'])
                    ->radios($field['values'])
                    ->label($field['label']) !!}
            </div>
        </div>
    @elseif($field['type'] == 'inline_radio')
        <div class="row">
            <div class="col-md-12">
                {!! Former::inline_radios($field['name'])
                    ->radios($field['values'])
                    ->label($field['label']) !!}
            </div>
        </div>
    @endif
@endforeach

{!! Former::hidden('intake_form')->value($intake_form) !!}
        
        