<?php

namespace Modules\WorkOrder\Models;

use App\Models\EntityModel;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderSettings extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'Modules\WorkOrderSettings\Presenters\WorkOrderSettingsPresenter';

    /**
     * @var string
     */
    protected $fillable = [];

    /**
     * @var string
     */
    protected $table = 'workorder';

    public function getEntityType()
    {
        return 'workorder';
    }

}
