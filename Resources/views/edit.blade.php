@extends('header')

@section('content')

    {{-- {{ Former::populateField('client_id', ($workorder && $workorder->client()->exists() ? $workorder->client->public_id : null)) }} --}}

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

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title in-white">
                        <i class="glyphicon glyphicon-pencil"></i> {{ mtrans('workorder', 'notes') }}
                    </h3>
                </div>
                <div class="panel-body">
                    {{-- @if($workorder->notes()->exists()) --}}
                    {{-- <table class="table table-striped">

                    <tbody>
                        @foreach($workorder->notes as $note)
                            <tr>
                                <td>{{ $note->created_at }}</td>
                                <td>{{ $note->note }}</td>
                                <td>{{ $note->user }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table> --}}
                    {{-- @else
                        <p>There are no notes yet for this work order</p>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
   
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

        {{-- {!! Button::normal(mtrans('workorder', 'add_note'))
            ->large()
            // ->data_toggle(['modal'])
            ->data_target(['#add_note_modal'])
            ->appendIcon(Icon::create('pluscircle'))
        !!} --}}


        <a href="#" class="btn btn-info btn-lg" data-toggle="modal" data-target="#add_note_modal">Add Note <i class="fa fa-plus-circle" style="padding-left: 12px;"></i></a>

    </center>

    {!! Former::close() !!}

    {!! Former::open($url . '/addnote')
        ->addClass('col-md-12 warn-on-exit')
        ->method('POST')
        ->rules([
            'work_order_date' => 'required',
            'synopsis' => 'required|max:80',
            'client_id' => 'required',
            'problem_description' => 'required',
        ]) !!}
    <div class="modal fade" id="add_note_modal" tabindex="-1" role="dialog" aria-labelledby="Add Note">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addNoteModalLabel">Add Note</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea class="form-control" name="add_note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                                <center class="buttons">
                                    {!! Button::success(trans('texts.save'))
                                        ->submit()
                                        ->appendIcon(Icon::create('floppy-disk'))
                                    !!}
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Former::close() !!}
        
    <script type="text/javascript">

        // $(function() {
        //     $(".warn-on-exit input").first().focus();
        // })

        $('#work_order_date').datepicker();

        $('.work_order_date .input-group-addon').click(function() {
            $('#work_order_date').datepicker('show');
        });

    </script>
    

@stop
