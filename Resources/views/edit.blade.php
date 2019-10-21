@extends('header')

@section('content')
    {!! Former::open($url)
            ->addClass('col-md-12 warn-on-exit')
            ->method($method)
            ->rules([
                'work_order_date' => 'required',
                'synopsis' => 'required|max:80',
                'client_id' => 'required',
                'problem_description' => 'required',
            ]) !!}

    @if ($workorder)
      {!! Former::populate($workorder) !!}
      <div style="display:none">
          {!! Former::text('public_id') !!}
      </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            @render('App\Http\ViewComponents\SimpleSelectComponent', ['entityType' => ENTITY_CLIENT, 'items' => $clients, 'itemLabel' => 'name', 'fieldLabel' => 'client'])
                            
                            @if($workorder)
                                {!! Former::text('work_order_number')->label(mtrans('workorder', 'work_order_number')) !!}
                            @endif

                            {!! Former::text('work_order_date')->label(mtrans('workorder', 'work_order_date'))
                                ->data_bind("datePicker: work_order_date, valueUpdate: 'afterkeydown'")
                                ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))->appendIcon('calendar')->addGroupClass('work_order_date') !!}

                            {!! Former::textarea('synopsis')->label(mtrans('workorder', 'synopsis'))->rows(3) !!}
                        </div>
                        <div class="col-md-8">
                                {!! Former::textarea('problem_description')->label(mtrans('workorder', 'problem_description'))->rows(8) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@if($workorder)
<style type="text/css">
    ul.timeline {
        list-style-type: none;
        position: relative;
    }

    ul.timeline:before {
        content: ' ';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
    }

    ul.timeline > li {
        margin: 20px 0;
        padding-left: 20px;
    }

    ul.timeline > li:before {
        content: ' ';
        background: white;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        left: 20px;
        width: 20px;
        height: 20px;
        z-index: 400;
    }
</style>

<div class="row">
    <div class="col-md-6" id="notes_container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title in-white">
                    <i class="glyphicon glyphicon-pushpin"></i> {{ mtrans('workorder', 'notes') }}
                </h3>
            </div>
            <div class="panel-body">
                <div class="input-group">
                    <textarea class="form-control" name="add_note" style="resize:none;" rows="3"></textarea>
                    <span id="addWorkOrderNote" class="input-group-addon btn btn-info disabled" style="background-color: #e27329">Add Note <i
                            class="fa fa-plus-circle" style="padding-left: 12px;"></i></span>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="timeline">
                            @forelse($notes as $note)
                            <li>
                                @include('workorder::partials.note')
                                {{-- <span style="font-weight: bold;">{{ $note->created_at }}</span>
                                <span style="float: right; font-style: italic;">{{ $note->user->getDisplayName() }}</span>
                                <p>{{ $note->note }}</p> --}}
                            </li>
                            @empty
                            <li class="timeline-empty">There are no notes yet!</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @if($intake || $intake_form)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title in-white">
                        <i class="glyphicon glyphicon-pencil"></i> {{ mtrans('workorder', 'intake_form') }}
                    </h3>
                </div>
                <div class="panel-body">
                    @include('workorder::partials.intake_form', $intake)
                </div>
            </div>
        @endif
    </div>
</div>
@endif
   
    <center class="buttons">

        {!! Button::normal(trans('texts.cancel'))
            ->large()
            ->asLinkTo(URL::to('/workorders'))
            ->appendIcon(Icon::create('remove-circle'))
        !!}

        {!! Button::success(trans('texts.save'))
            ->submit()
            ->large()
            ->appendIcon(Icon::create('floppy-disk'))
        !!}
    </center>

    {!! Former::close() !!}
@stop

@section('onReady')
    $('#work_order_date').datepicker();

    $('.work_order_date .input-group-addon').click(function() {
        $('#work_order_date').datepicker('show');
    });

    $('[name="add_note"]').on('keyup', function() {
        console.log(this);
        if($(this).val() == "") {
            $('#addWorkOrderNote').addClass('disabled');
        } else {
            $('#addWorkOrderNote').removeClass('disabled');
        }
    });

    $('#addWorkOrderNote').on('click', function () {
        if($('[name="add_note"]').val() !== "") {
            addNote();
        }
    });
  
    function addNote() {
        var note = $('[name="add_note"]').val();
        console.log(note);
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("workorders.addnote", ['workorder' => $workorder]) }}',
            data: {
                'note': note
            },
            error: function() {
                console.log('An error occurred during the Ajax request.');
            },
            success: function(data) {
                $('.timeline-empty').remove();

                $('.timeline').prepend('<li>' + data.html + '</li>');
                                
                $('[name="add_note"]').val('');
                $('#addWorkOrderNote').addClass('disabled');
            },
            type: 'POST'
        });
    }
{{-- @endpush --}}
@endsection

