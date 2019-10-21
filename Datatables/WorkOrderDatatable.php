<?php

namespace Modules\WorkOrder\Datatables;

use Utils;
use URL;
use Auth;
use App\Ninja\Datatables\EntityDatatable;

class WorkOrderDatatable extends EntityDatatable
{
    public $entityType = 'workorder';
    public $sortCol = 2;

    public function columns()
    {
        return [
            [
                'work_order_number',
                function ($model) {
                    return link_to("workorders/{$model->id}", $model->work_order_number)->toHtml();
                }
            ],
            [
                'work_order_date',
                function ($model) {
                    // return Utils::fromSqlDateTime($model->work_order_date);
                    return $model->work_order_date;
                },
            ],
            [
                'client_name',
                function($model) {
                    $model->entityType = ENTITY_CLIENT;
                    if(Auth::user()->can('viewModel', $model)) {
                        return link_to("clients/{$model->client_public_id}", Utils::getClientDisplayName($model))->toHtml();
                    } else {
                        return Utils::getClientDisplayName($model);
                    }
                },
            ],
            [
                'synopsis',
                function ($model) {
                    return $model->synopsis;
                },
            ],
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
                    return URL::to("workorders/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', ['workorder', $model->user_id]);
                }
            ],
        ];
    }

}
