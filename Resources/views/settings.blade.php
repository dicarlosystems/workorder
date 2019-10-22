@extends('header')

@section('content')
@parent

@include('accounts.nav', ['selected' => 'WorkOrder'])

{!! Former::open('settings/workorder') !!}

{{ Former::populate($account) }}
{!! Former::populateField('work_order_number_counter', $settings->work_order_number_counter) !!}
{!! Former::populateField('work_order_number_prefix', $settings->work_order_number_prefix) !!}
{!! Former::populateField('work_order_number_pattern', $settings->work_order_number_pattern) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Work Order Number</h3>
    </div>
    <div class="panel-body">
        {!! Former::inline_radios('work_order_number_type')
            ->onchange("onNumberTypeChange('work_order')")
            ->label(trans('texts.type'))
            ->radios([
                trans('texts.prefix') => ['value' => 'prefix', 'name' => 'work_order_number_type'],
                trans('texts.pattern') => ['value' => 'pattern', 'name' => 'work_order_number_type'],
            ])->check($settings->work_order_number_pattern ? 'pattern' : 'prefix') !!}

        {!! Former::text('work_order_number_prefix')
            ->addGroupClass('work_order-prefix')
            ->label(trans('texts.prefix')) !!}
        
        {!! Former::text('work_order_number_pattern')
            ->appendIcon('question-sign')
            ->addGroupClass('work_order-pattern')
            ->label(trans('texts.pattern'))
            ->addGroupClass('number-pattern') !!}
        
        {!! Former::text('work_order_number_counter')
            ->label(trans('texts.counter'))
            ->help(mtrans('workorder', 'work_order_number_help') . ' ' .
                trans('workorder::texts.next_work_order_number', ['number' => $nextNumberPreview])) !!}
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Intake Form</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                {!! Former::textarea('intake_form')
                    ->label(mtrans('workorder','intake_form'))
                    ->rows(8)
                    ->raw() !!}
            </div>
            <div class="col-md-6">
                <p>TODO!!! Help content goes here</p>
            </div>
        </div>
    </div>
</div>

<center>
    {!! Button::success(trans('texts.save'))->large()->submit()->appendIcon(Icon::create('floppy-disk')) !!}
</center>

<div class="modal fade" id="patternHelpModal" tabindex="-1" role="dialog" aria-labelledby="patternHelpModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="min-width:150px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="patternHelpModalLabel">{{ trans('texts.pattern_help_title') }}</h4>
            </div>

            <div class="container" style="width: 100%; padding-bottom: 0px !important">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>{{ trans('texts.pattern_help_1') }}</p>
                        <p>{{ trans('texts.pattern_help_2') }}</p>
                        <ul>
                            @foreach ($patternFields as $field)
                            @if ($field == 'date:')
                            <li>{$date:format} - {!! link_to(PHP_DATE_FORMATS, trans('texts.see_options'), ['target' =>
                                '_blank']) !!}</li>
                            @elseif (strpos($field, 'client') !== false)
                            <li class="hide-client">{${{ $field }}}</li>
                            @else
                            <li>{${{ $field }}}</li>
                            @endif
                            @endforeach
                        </ul>
                        <p class="hide-client">{{ trans('texts.pattern_help_3', [
                            'example' => '{$year}-{$counter}',
                            'value' => date('Y') . '-0001'
                        ]) }}</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('texts.close') }}</button>
            </div>

        </div>
    </div>
</div>

{!! Former::close() !!}

<script type="text/javascript">
    function onNumberTypeChange(entityType) {
        var val = $('input[name=' + entityType + '_number_type]:checked').val();
        if (val == 'prefix') {
            $('.' + entityType + '-prefix').show();
            $('.' + entityType + '-pattern').hide();
        } else {
            $('.' + entityType + '-prefix').hide();
            $('.' + entityType + '-pattern').show();
        }
    }

    $('.number-pattern .input-group-addon').click(function() {
        $('.hide-client').show();
        $('#patternHelpModal').modal('show');
    });

    $(function() {
        onNumberTypeChange('work_order');
    });
</script>
@stop