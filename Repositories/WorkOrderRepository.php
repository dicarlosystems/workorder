<?php

namespace Modules\WorkOrder\Repositories;

use DB;
use Utils;
use App\Models\Client;
use Modules\WorkOrder\Models\WorkOrder;
use App\Ninja\Repositories\BaseRepository;
use Modules\WorkOrder\Services\WorkOrderService;
//use App\Events\WorkorderWasCreated;
//use App\Events\WorkorderWasUpdated;

class WorkOrderRepository extends BaseRepository
{
    protected $workorderService;

    public function __construct(WorkOrderService $workorderService)
    {
        //parent::__construct();

        $this->workorderService = $workorderService;
    }

    public function getClassName()
    {
        return 'Modules\Workorder\Models\WorkOrder';
    }

    public function all()
    {
        return Workorder::scope()
                ->orderBy('created_at', 'desc')
                ->withTrashed();
    }

    public function find($filter = null, $userId = false)
    {
        $query = DB::table('workorders')
                    ->join('clients', 'workorders.client_id', '=', 'clients.id')
                    ->where('workorders.account_id', '=', \Auth::user()->account_id)
                    ->select(
                        'workorders.id',
                        'workorders.work_order_number',
                        'workorders.client_id',
                        'workorders.work_order_date',
                        'workorders.synopsis',
                        'workorders.problem_description',
                        'workorders.intake_data',
                        'workorders.public_id',
                        'workorders.deleted_at',
                        'workorders.created_at',
                        'workorders.is_deleted',
                        'workorders.user_id',
                        'clients.name as client_name',
                        'clients.public_id as client_public_id'
                    );

        $this->applyFilters($query, 'workorder');

        if ($userId) {
            $query->where('clients.user_id', '=', $userId);
        }

        /*
        if ($filter) {
            $query->where();
        }
        */

        return $query;
    }

    public function save($data, $workorder = null)
    {
        $entity = $workorder ?: WorkOrder::createNew();
        $client = Client::findOrFail($data['client_id']);
        $entity->client()->associate($client);

        $entity->fill($data);
        $entity->work_order_date = Utils::toSqlDate($data['work_order_date']);
        
        $entity->work_order_number = $this->workorderService->getNextNumber($entity);

        $entity->save();

        /*
        if (!$publicId || intval($publicId) < 0) {
            event(new ClientWasCreated($client));
        } else {
            event(new ClientWasUpdated($client));
        }
        */

        return $entity;
    }
}
