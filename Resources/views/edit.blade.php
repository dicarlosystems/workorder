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

    <style>

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
@if($workorder)
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title in-white">
                        <i class="glyphicon glyphicon-pushpin"></i> {{ mtrans('workorder', 'notes') }}
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <textarea class="form-control" name="add_note" style="resize:none;" rows="3"></textarea>
                        <span class="input-group-addon btn btn-info" style="background-color: #e27329">Add Note <i class="fa fa-plus-circle" style="padding-left: 12px;"></i></span>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="timeline">
                                @forelse($notes as $note)
                                <li>
                                    <span style="font-weight: bold;">{{ $note->created_at }}</span>
                                <span style="float: right; font-style: italic;">{{ $note->user->getDisplayName() }}</span>
                                <p>{{ $note->note }}</p>
                                </li>
                                @empty
                                    <li>There are no notes yet!</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title in-white">
                        <i class="glyphicon glyphicon-paperclip"></i> {{ trans('texts.documents') }}
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-12" style="padding-top: 12px;"> --}}
                            {{-- {!! Button::normal(trans('texts.save'))
                                ->submit()
                                ->appendIcon(Icon::create('floppy-disk'))
                            !!} --}}
                            {{-- <a href="#" class="btn btn-default" data-toggle="modal" data-target="#add_note_modal">Add Note <i class="fa fa-plus-circle" style="padding-left: 12px;"></i></a> --}}
                        {{-- </div>
                    </div> --}}
                </div>
            </div>  
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

        {{-- {!! Button::normal(mtrans('workorder', 'add_note'))
            ->large()
            // ->data_toggle(['modal'])
            ->data_target(['#add_note_modal'])
            ->appendIcon(Icon::create('pluscircle'))
        !!} --}}

        {{-- @if($workorder)
            <a href="#" class="btn btn-info btn-lg" data-toggle="modal" data-target="#add_note_modal">Add Note <i class="fa fa-plus-circle" style="padding-left: 12px;"></i></a>
        @endif --}}

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
