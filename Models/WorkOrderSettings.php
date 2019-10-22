<?php

namespace Modules\WorkOrder\Models;

use App\Models\EntityModel;
use Laracasts\Presenter\PresentableTrait;


class WorkOrderSettings extends EntityModel
{
    use PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'Modules\WorkOrderSettings\Presenters\WorkOrderSettingsPresenter';

    /**
     * @var string
     */
    protected $fillable = [
        'work_order_number_counter',
        'work_order_number_prefix',
        'work_order_number_pattern',
        'account_id'
    ];

    protected $table = 'work_order_settings';

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }
}
