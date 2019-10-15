<?php

namespace Modules\WorkOrder\Datatables;

use Utils;
use URL;
use Auth;
use App\Ninja\Datatables\EntityDatatable;

class WorkOrderDatatable extends EntityDatatable
{
    public $entityType = 'workorder';
    public $sortCol = 1;

    public function columns()
    {
        return [
            
            [
                'created_at',
                function ($model) {
                    return Utils::fromSqlDateTime($model->created_at);
                }
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                mtrans('workorder', 'edit_workorder'),
                function ($model) {
                    return URL::to("workorder/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', ['workorder', $model->user_id]);
                }
            ],
        ];
    }

}
