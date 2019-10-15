<?php

namespace Modules\WorkOrder\Repositories;

use DB;
use Modules\Workorder\Models\Workorder;
use App\Ninja\Repositories\BaseRepository;
//use App\Events\WorkorderWasCreated;
//use App\Events\WorkorderWasUpdated;

class WorkorderRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'Modules\Workorder\Models\Workorder';
    }

    public function all()
    {
        return Workorder::scope()
                ->orderBy('created_at', 'desc')
                ->withTrashed();
    }

    public function find($filter = null, $userId = false)
    {
        $query = DB::table('workorder')
                    ->where('workorder.account_id', '=', \Auth::user()->account_id)
                    ->select(
                        
                        'workorder.public_id',
                        'workorder.deleted_at',
                        'workorder.created_at',
                        'workorder.is_deleted',
                        'workorder.user_id'
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
        $entity = $workorder ?: Workorder::createNew();

        $entity->fill($data);
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
