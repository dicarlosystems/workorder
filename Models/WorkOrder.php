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
        'client_id',
        'intake_data',
        'intake_form',
        'problem_description',
        'synopsis',
        'user_id',
        'work_order_number'
    ];

    protected $dates = [
        'workorder_date',
    ];

    protected $casts = [
        'intake_data' => 'array',
        'intake_form' => 'array'
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

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function setIntakeDataAttribute($value) {
        $fields = json_decode($value, true);
        
        if(is_array($fields)) {
            $keys = array_keys($fields);
            
            for($i = 0; $i < count($keys); $i++) {
                $keys[$i] = str_replace('intake_', '', $keys[$i]);
            }

            $this->attributes['intake_data'] = json_encode(array_combine($keys, $fields), true);
        }

        return null;
    }

    // public function getIntakeDataAttribute($value) {
    //     $fields = json_decode($value, true);
        
    //     if(! $fields) return [];

    //     return $fields;
    // }
}
