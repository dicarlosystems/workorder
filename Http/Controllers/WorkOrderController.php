<?php

namespace Modules\WorkOrder\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Client;
use App\Services\DatatableService;
use Auth;
use Modules\Manufacturer\Models\Manufacturer;
use Modules\WorkOrder\Datatables\WorkOrderDatatable;
use Modules\WorkOrder\Http\Requests\CreateWorkOrderRequest;
use Modules\WorkOrder\Http\Requests\UpdateWorkOrderRequest;
use Modules\WorkOrder\Http\Requests\WorkOrderRequest;
use Modules\WorkOrder\Models\WorkOrderNote;
use Modules\WorkOrder\Repositories\WorkOrderRepository;
use ReflectionClass;
use ReflectionMethod;
use Utils;

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
        $data = $request->input();

        $workorder = $this->workorderRepo->save($data);

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

        $intake_data = json_decode($workorder->intake_data, true);

        $intake_json = [

            // radio format:
            // [0] type == radio
            // [1] Comma-separated list of values
            'Power Cord' => 'inline_radio|Yes,No,N/A',
            'Powers On?' => 'radio|Yes,No,Unknown',
            'E-Waste after complete?' => 'select|Yes,No,N/A',
            
            // simple select format:
            // [0] type == simpleselect
            // [1] Entity class
            // [2] Sort field
            // [3] itemLabel
            // [4] fieldLabel
            // [5] Module name
            'Manufacturer' => 'simpleselect|Modules\Manufacturer\Models\Manufacturer|name|name|fieldLabel|Manufacturer',
            'Product' => 'simpleselect|App\Models\Product|product_key|product_key|fieldLabel',
            'Username' => 'text',
            'Password' => 'text'
        ];

        $intake = [];

        foreach($intake_json as $fieldName => $attributeString) {
            $attributes = explode('|', $attributeString);

            if($attributes[0] == 'text' || $attributes[0] == 'textarea') {
                $intake[] = [
                    'type' => $attributes[0],
                    'name' => 'intake_' . str_replace(' ', '_', $fieldName),
                    'label' => $fieldName,
                    'value' => $intake_data['intake_' . $fieldName]
                ];
            // } elseif($attributes[0] =='radio' || $attributes[0] == 'select') {
            } elseif($attributes[0] =='simpleselect') {
                $className = $attributes[1];
                
                $values = $className::get()->sortBy($attributes[2]);
                $entityType = array(new $className, "getEntityType");

                $intake[] = [
                    'type' => $attributes[0],
                    'entityType' => $entityType(),
                    'items' => $values,
                    'itemLabel' => $attributes[3],
                    'fieldLabel' => $entityType(),
                    'module' => array_key_exists(5, $attributes) ? $attributes[5] : null,
                    'selectId' => 'intake_' . $entityType()
                ];
            } elseif($attributes[0] == 'radio' || $attributes[0] == 'inline_radio') {
                $values = explode(',', $attributes[1]);
                $radios = [];

                foreach($values as $key => $value) {
                    $radios[$value] = [
                        'name' => 'intake_' . str_replace(' ', '_', $fieldName),
                        'value' => $value
                    ];
                }

                $intake[] = [
                    'type' => $attributes[0],
                    'name' => 'intake_' . str_replace(' ', '_', $fieldName),
                    'values' => $radios,
                    'value' => $intake_data['intake_' . str_replace(' ', '_', $fieldName)]
                ];
            } elseif($attributes[0] == 'select') {
                $values = explode(',', $attributes[1]);
                $options = [];

                foreach($values as $key => $value) {
                    $options[$value] = [
                        'name' => $fieldName,
                        'value' => $value
                    ];
                }

                $intake[] = [
                    'type' => $attributes[0],
                    'name' => 'intake_' . str_replace(' ', '_', $fieldName),
                    'values' => $options,
                ];
            }
        }

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
        $data = $request->input();

        $intake_data = [];

        foreach($data as $field => $value) {
            if(substr($field, 0, 7) == 'intake_') {
                $intake_data[$field] = $value;
            }
        }

        $request->merge(['intake_data' => json_encode($intake_data)]);

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

    public function saveSettings() {

    }

    public function showSettings()
    {
        $account = Auth::user()->account;

        return view('workorder::settings', [
            'account' => $account
        ]);
    }
}
