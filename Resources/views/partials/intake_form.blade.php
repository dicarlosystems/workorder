@foreach($intake as $field)
    @if($field['type'] == 'text')
        <div class="row">
            <div class="col-md-12">
                {!! Former::text($field['name'])->label($field['label']) !!}
            </div>
        </div>
    @elseif($field['type'] == 'simpleselect')
        <div class="row">
            <div class="col-md-12">
                @render('App\Http\ViewComponents\SimpleSelectComponent', [
                    'entityType' => $field['entityType'],
                    'items' => $field['items'],
                    'itemLabel' => $field['itemLabel'],
                    'fieldLabel' => $field['fieldLabel'],
                    'module' => $field['module'],
                    'selectId' => $field['selectId']
                ])
            </div>
        </div>
    @elseif($field['type'] == 'select')
        <div class="row">
            <div class="col-md-12">
                {!! Former::select($field['name'])
                    ->options($field['values']) !!}
            </div>
        </div>
    @elseif($field['type'] == 'radio')
        <div class="row">
            <div class="col-md-12">
                {!! Former::radios($field['name'])
                ->radios($field['values']) !!}
            </div>
        </div>
    @elseif($field['type'] == 'inline_radio')
        <div class="row">
            <div class="col-md-12">
                {!! Former::inline_radios($field['name'])
                    ->radios($field['values']) !!}
                    {{ Former::populateField($field['name'], $field['value']) }}
            </div>
        </div>
    @endif
    {{-- {{ dd($field) }} --}}
    
@endforeach

