<?php

namespace Modules\WorkOrder\Models;

use App\Models\EntityModel;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderNote extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'Modules\WorkOrderNote\Presenters\WorkOrderNotePresenter';

    /**
     * @var string
     */
    protected $fillable = [
        'note',
        'note_date',
    ];

    protected $dates = [
        'note_date',
    ];

    /**
     * @var string
     */
    protected $table = 'workorder_notes';

    public function getEntityType()
    {
        return 'workorder_note';
    }

}
