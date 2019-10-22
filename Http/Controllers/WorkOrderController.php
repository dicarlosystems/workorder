<?php

namespace Modules\WorkOrder\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Client;
use App\Services\DatatableService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\WorkOrder\Datatables\WorkOrderDatatable;
use Modules\WorkOrder\Http\Requests\CreateWorkOrderRequest;
use Modules\WorkOrder\Http\Requests\UpdateWorkOrderRequest;
use Modules\WorkOrder\Http\Requests\WorkOrderRequest;
use Modules\WorkOrder\Models\WorkOrder;
use Modules\WorkOrder\Models\WorkOrderNote;
use Modules\WorkOrder\Repositories\WorkOrderRepository;
use Modules\WorkOrder\Services\WorkOrderService;
use Utils;

class WorkOrderController extends BaseController
{
    protected $workorderRepo;
    protected $workorderService;
    //protected $entityType = 'workorder';

    public function __construct(WorkOrderRepository $workorderRepo, WorkOrderService $workorderService)
    {
        //parent::__construct();

        $this->workorderRepo = $workorderRepo;
        $this->workorderService = $workorderService;
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

        $intake_data = $workorder->intake_data;
        $intake_form = $this->workorderService->getIntakeForm($workorder);
    
        // intake data format:
        // [key] form field name
        // [0] type == radio || inline_radio || select || text || textarea
        // [1] label
        // [2] Comma-separated list of values (* only for types: radio, inline_radio, and select)

        // $intake_form = [
        //     'power_cord' => 'inline_radio|Power Cord|Yes,No,N/A',
        //     'powers_on' => 'radio|Powers On?|Yes,No,Unknown',
        //     'ewaste_after' => 'select|E-Waste after complete?|Yes,No,N/A',
        //     'manufacturer' => 'text|Manufacturer',
        //     'username' => 'text|Username',
        //     'password' => 'text|Password'
        // ];

        // dump(json_encode($intake_form, true));

        $intake = [];

        if($intake_form) {
            foreach($intake_form as $fieldName => $attributeString) {
                $attributes = explode('|', $attributeString);

                if($attributes[0] == 'text' || $attributes[0] == 'textarea') {
                    $intake[] = [
                        'type' => $attributes[0],
                        'name' => 'intake_' . str_replace(' ', '_', $fieldName),
                        'label' => $attributes[1],
                        'value' => $intake_data ? (array_key_exists($fieldName, $intake_data) ? $intake_data[$fieldName] : '') : ''
                    ];
                } elseif($attributes[0] == 'radio' || $attributes[0] == 'inline_radio') {
                    $values = explode(',', $attributes[2]);
                    $radios = [];

                    foreach($values as $key => $value) {
                        $radios[$value] = [
                            'name' => 'intake_' . str_replace(' ', '_', $fieldName),
                            'value' => $value,
                            'checked' => $intake_data && array_key_exists($fieldName, $intake_data) ? ($intake_data[$fieldName] == $value ? true : false) : false
                        ];
                    }

                    $intake[] = [
                        'type' => $attributes[0],
                        'name' => 'intake_' . str_replace(' ', '_', $fieldName),
                        'label' => $attributes[1],
                        'values' => $radios
                    ];
                } elseif($attributes[0] == 'select') {
                    $values = explode(',', $attributes[2]);
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
                        'label' => $attributes[1],
                        'values' => $options,
                        'value' => $intake_data ? (array_key_exists($fieldName, $intake_data) ? $intake_data[$fieldName] : '') : null
                    ];
                }
            }
        }

        $data = [
            'workorder' => $workorder,
            'method' => 'PUT',
            'url' => 'workorders/' . $workorder->public_id,
            'title' => mtrans('workorder', 'edit_workorder'),
            'clients' => $clients,
            'notes' => $notes,
            'intake' => $intake,
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

        return response()->json(['html' => $html, 'message' => mtrans('workorder', 'added_note')]);
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

    public function saveSettings(Request $request) {
        $account = Auth::user()->account;

        $settings = $this->workorderService->getSettings($account);

        $settings->work_order_number_counter = $request->input('work_order_number_counter');
        
        if ($request->input("work_order_number_type") == 'prefix') {
            $settings->work_order_number_prefix = trim($request->input('work_order_number_prefix'));
            $settings->work_order_number_pattern = null;
        } else {
            $settings->work_order_number_prefix = null;
            $settings->work_order_number_pattern = trim($request->input('work_order_number_pattern'));
        }

        $settings->intake_form = trim($request->input('intake_form'));

        $settings->save();

        return Redirect::to('settings/workorder');
    }

    public function showSettings()
    {
        $account = Auth::user()->account;
        $settings = $this->workorderService->getSettings($account);
        $nextNumberPreview = $this->workorderService->previewNextNumber();

        $patternFields = WorkOrder::$patternFields;

        return view('workorder::settings', [
            'account' => $account,
            'settings' => $settings,
            'nextNumberPreview' => $nextNumberPreview,
            'patternFields' => $patternFields,
        ]);
    }
}
