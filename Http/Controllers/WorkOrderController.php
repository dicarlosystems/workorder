<?php

namespace Modules\WorkOrder\Http\Controllers;

use Auth;
use Utils;
use ReflectionClass;
use ReflectionMethod;
use App\Models\Client;
use App\Services\DatatableService;
use App\Http\Controllers\BaseController;
use Modules\WorkOrder\Models\WorkOrderNote;
use Modules\Manufacturer\Models\Manufacturer;
use Modules\WorkOrder\Datatables\WorkOrderDatatable;
use Modules\WorkOrder\Http\Requests\WorkOrderRequest;
use Modules\WorkOrder\Repositories\WorkOrderRepository;
use Modules\WorkOrder\Http\Requests\CreateWorkOrderRequest;
use Modules\WorkOrder\Http\Requests\UpdateWorkOrderRequest;

class WorkOrderController extends BaseController
{
    protected $WorkOrderRepo;
    //protected $entityType = 'workorder';

    public function __construct(WorkOrderRepository $workorderRepo)
    {
        //parent::__construct();

        $this->workorderRepo = $workorderRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('list_wrapper', [
            'entityType' => 'workorder',
            'datatable' => new WorkOrderDatatable(),
            'title' => mtrans('workorder', 'workorder_list')
        ]);
    }

    public function datatable(DatatableService $datatableService)
    {
        $search = request()->input('sSearch');
        $userId = Auth::user()->filterId();

        $datatable = new WorkOrderDatatable();
        $query = $this->workorderRepo->find($search, $userId);

        return $datatableService->createDatatable($datatable, $query);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(WorkOrderRequest $request)
    {
        $clients = Client::all()->map(function($item) {
            return ['value' => $item->name . ' - ' . $item->id_number, 'key' => $item->id];
        })->pluck('value', 'key');

        $data = [
            'workorder' => null,
            'method' => 'POST',
            'url' => 'workorders',
            'title' => mtrans('workorder', 'new_workorder'),
            'clients' => $clients,
        ];

        return view('workorder::edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(CreateWorkOrderRequest $request)
    {
        $workorder = $this->workorderRepo->save($request->input());

        return redirect()->to($workorder->present()->editUrl)
            ->with('message', mtrans('workorder', 'created_workorder'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit(WorkOrderRequest $request)
    {
        $workorder = $request->entity();

        $notes = WorkOrderNote::where('work_order_id', '=', $workorder->id)->orderBy('created_at', 'desc')->get();

        $clients = Client::all()->map(function($item) {
            return ['value' => $item->name . ' - ' . $item->id_number, 'key' => $item->id];
        })->pluck('value', 'key');

        $workorder->work_order_date = Utils::fromSqlDate($workorder->work_order_date);

        $intake_json = [
            'Power Cord' => 'radio|direct|Y,N',
            'Powers On?' => 'radio|direct|Y,N',
            'Manufacturer' => 'select|eloquent|Modules\Manufacturer\Models\Manufacturer|name',
            'Username' => 'text',
            'Password' => 'text'
        ];

        $intake = [];

        foreach($intake_json as $fieldName => $attributeString) {
            $attributes = explode('|', $attributeString);

            if($attributes[0] == 'text' || $attributes[0] == 'textarea') {
                $intake[] = [
                    'label' => $fieldName,
                    'type' => $attributes[0]
                ];
            } elseif($attributes[0] =='radio' || $attributes[0] == 'select') {
                if($attributes[1] == 'direct') {
                    $values = explode(',', $attributes[2]);
                } elseif($attributes[1] == 'eloquent') {
                    $className = $attributes[2];
                    
                    $values = $className::get()->sortBy('name');
                }

                $intake[] = [
                    'label' => $fieldName,
                    'type' => $attributes[0],
                    'values' => $values
                ];

            } elseif($attributes[0] == 'checkbox') {
            }
        }

        dump($intake);
      
        $data = [
            'workorder' => $workorder,
            'method' => 'PUT',
            'url' => 'workorders/' . $workorder->public_id,
            'title' => mtrans('workorder', 'edit_workorder'),
            'clients' => $clients,
            'notes' => $notes,
            'intake' => $intake
        ];

        return view('workorder::edit', $data);
    }

    public function addNote(WorkOrderRequest $request)
    {
        $workorder = $request->entity();
        $note = WorkOrderNote::createNew();

        $note->fill($request->input());

        $workorder->notes()->save($note);

        $html = view('workorder::partials.note', ['note' => $note])->render();

        // return redirect()->to("workorders/{$request->workorder}/edit");
        // return response()->json(['html' => $html]);
        return response()->json(['html' => $html]);
    }

    /**
     * Show the form for editing a resource.
     * @return Response
     */
    public function show(WorkOrderRequest $request)
    {
        return redirect()->to("workorders/{$request->workorder}/edit");
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(UpdateWorkOrderRequest $request)
    {
        $workorder = $this->workorderRepo->save($request->input(), $request->entity());

        return redirect()->to($workorder->present()->editUrl)
            ->with('message', mtrans('workorder', 'updated_workorder'));
    }

    /**
     * Update multiple resources
     */
    public function bulk()
    {
        $action = request()->input('action');
        $ids = request()->input('public_id') ?: request()->input('ids');
        $count = $this->workorderRepo->bulk($ids, $action);

        return redirect()->to('workorders')
            ->with('message', mtrans('workorder', $action . '_workorder_complete'));
    }
}
