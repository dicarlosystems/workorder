<?php

namespace Modules\WorkOrder\Http\Requests;

use App\Http\Requests\EntityRequest;

class WorkOrderRequest extends EntityRequest
{
    protected $entityType = 'workorder';
}
