<?php

namespace Modules\WorkOrder\Services;

use App\Services\BaseService;
use Modules\WorkOrder\Models\WorkOrder;
use Modules\WorkOrder\Models\WorkOrderSettings;

class WorkOrderService extends BaseService
{
    public function getNextNumber(WorkOrder $entity)
    {
        $account = $entity->account;
        $settings = WorkOrderSettings::where('account_id', '=', $account->id)->first();

        if($settings) {
            $counter = $settings->work_order_number_counter;
            $prefix = $settings->work_order_number_prefix ?: '';
            $counterOffset = 0;
            $numberExists = false;
            $lastNumber = false;

            do {
                $number = $prefix . str_pad($counter, $settings->work_order_number_padding, '0', STR_PAD_LEFT);

                $numberExists = WorkOrder::scope(false, $account->id)->whereWorkOrderNumber($number)->withTrashed()->first();

                $counter++;
                $counterOffset++;

                if($number == $lastNumber) {
                    return '';
                }

                $lastNumber = $number;
            } while($numberExists);

            if($counter > 1) {
                $settings->syncOriginal();
                $settings->work_order_number_counter += $counterOffset - 1;
                $settings->save();
            }
        } else {
            $number = '';
        }

        return $number;
    }

    public function getIntakeForm(WorkOrder $workOrder)
    {
        if($workOrder->intake_form) {
            return json_decode($workOrder->intake_form, true);
        } else {
            $settings = WorkOrderSettings::where('account_id', '=', $workOrder->account->id)->first();
            
            if($settings) {
                return json_decode($settings->intake_form, true);
            }
        }

        return '';
    }
}
