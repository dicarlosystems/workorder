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
                            
                            {!! Former::text('work_order_date')->label(mtrans('workorder', 'work_order_date'))
                                ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))->appendIcon('calendar')->addGroupClass('work_order_date') !!}

                            {!! Former::textarea('synopsis')->label(mtrans('workorder', 'synopsis'))->rows(3) !!}
                        </div>
                        {{-- <div class="col-md-8">
                            {!! Former::textarea('problem_description')->label(mtrans('workorder', 'problem_description'))->rows(7)->columns(20) !!}
                            {!! Button::normal(mtrans('workorder', 'add_note'))
                            // ->success()
                            ->asLinkTo(URL::to('/workorders'))
                            ->appendIcon(Icon::create('plus-sign')) !!}
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title in-white">{{ mtrans('workorder', 'add_note') }}</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea rows="5" class="form-control" id="note" name="note"></textarea>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1rem;">
                        <div class="col-md-12 text-center">
                            <a href="#" class="btn btn-info" data-toggle="modal" data-target="#add_note_modal"><i class="fa fa-plus-sign"></i> Add Note</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title in-white">{{ mtrans('workorder', 'notes') }}</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans('texts.date') }}</th>
                                    <th>{{ mtrans('workorder', 'note') }}</th>
                                    <th>{{ trans('texts.user') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($workorder)
                                @forelse($workorder->notes as $note)
                                <tr>
                                    <td>{{ $note->note_date }}</td>
                                    <td>{{ $note->syposis }}</td>
                                    <td>{{ $note->user_name }}</td>
                                </tr>
                                @empty
                                    <tr>
                                        <td span="3">
                                            {{ mtrans('workorder', 'empty_notes_table') }}
                                        </td>
                                    </tr>
                                @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <center class="buttons">

        {!! Button::normal(trans('texts.cancel'))
                ->large()
                ->asLinkTo(URL::to('/workorders'))
                ->appendIcon(Icon::create('remove-circle')) !!}

        {!! Button::success(trans('texts.save'))
                ->submit()
                ->large()
                ->appendIcon(Icon::create('floppy-disk')) !!}

    </center>

    {!! Former::close() !!}

    <div class="modal fade" id="add_note_modal" tabindex="-1" role="dialog" aria-labelledby="Add Note">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="scannerModalLabel">Point Of Sale Scanning</h4>
                    </div>
                    <div class="modal-body">
                        {!! Former::text('pointofsale_barcode')->label('')->addClass('form-control text-center')->placeholder('Barcode') !!}
                    </div>
                </div>
            </div>
        </div>
        


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
