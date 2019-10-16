<?php

namespace Modules\WorkOrder\Models;

use App\Models\EntityModel;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'Modules\WorkOrder\Presenters\WorkOrderPresenter';

    /**
     * @var string
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'synopsis',
        'problem_description'
    ];

    protected $dates = [
        'workorder_date',
    ];

    /**
     * @var string
     */
    protected $table = 'workorders';

    public function getEntityType()
    {
        return 'workorder';
    }

    public function notes()
    {
        return $this->hasMany('Modules\WorkOrder\Models\WorkOrderNote');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

}
