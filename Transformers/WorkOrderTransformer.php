<?php

namespace Modules\WorkOrder\Transformers;

use Modules\Workorder\Models\Workorder;
use App\Ninja\Transformers\EntityTransformer;

/**
 * @SWG\Definition(definition="Workorder", @SWG\Xml(name="Workorder"))
 */

class WorkorderTransformer extends EntityTransformer
{
    /**
    * @SWG\Property(property="id", type="integer", example=1, readOnly=true)
    * @SWG\Property(property="user_id", type="integer", example=1)
    * @SWG\Property(property="account_key", type="string", example="123456")
    * @SWG\Property(property="updated_at", type="integer", example=1451160233, readOnly=true)
    * @SWG\Property(property="archived_at", type="integer", example=1451160233, readOnly=true)
    */

    /**
     * @param Workorder $workorder
     * @return array
     */
    public function transform(Workorder $workorder)
    {
        return array_merge($this->getDefaults($workorder), [
            
            'id' => (int) $workorder->public_id,
            'updated_at' => $this->getTimestamp($workorder->updated_at),
            'archived_at' => $this->getTimestamp($workorder->deleted_at),
        ]);
    }
}
