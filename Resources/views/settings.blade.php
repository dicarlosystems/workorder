@extends('header')

@section('content')
@parent

@include('accounts.nav', ['selected' => 'WorkOrder'])

{!! Former::open('settings/workorder') !!}
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Work Order Number</h3>
    </div>
    <div class="panel-body">
        {!! Former::inline_radios('work_order_number_type')
            ->onchange("onNumberTypeChange('workorder')")
            ->label(trans('texts.type'))
            ->radios([
                trans('texts.prefix') => ['value' => 'prefix', 'name' => 'work_order_number_type'],
                trans('texts.pattern') => ['value' => 'pattern', 'name' => 'work_order_number_type'],
            ])->check($account->invoice_number_pattern ? 'pattern' : 'prefix') !!}
        {!! Former::text('work_order_number_prefix')
            ->addGroupClass('workorder-prefix')
            ->label(trans('texts.prefix')) !!}
        
        {!! Former::text('work_order_number_pattern')
            ->appendIcon('question-sign')
            ->addGroupClass('workorder-pattern')
            ->label(trans('texts.pattern'))
            ->addGroupClass('number-pattern') !!}
        
        {!! Former::text('work_order_number_counter')
            ->label(trans('texts.counter'))
            ->help(trans('texts.invoice_number_help') . ' ' .
                trans('texts.next_invoice_number', ['number' => $account->previewNextInvoiceNumber()])) !!}
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
</script>
@stop