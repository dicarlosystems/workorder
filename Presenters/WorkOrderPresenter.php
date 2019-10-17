<?php

namespace Modules\WorkOrder\Presenters;

use App\Ninja\Presenters\EntityPresenter;

class WorkorderPresenter extends EntityPresenter
{
    public function work_order_date()
    {
        return Utils::fromSqlDate($this->entity->work_order_date);
    }
}
