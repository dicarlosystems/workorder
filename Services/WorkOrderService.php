<?php

namespace Modules\WorkOrder\Services;

use App\Models\Account;
use App\Services\BaseService;
use Carbon\Carbon;
use Modules\WorkOrder\Models\WorkOrder;
use Modules\WorkOrder\Models\WorkOrderSettings;

class WorkOrderService extends BaseService
{
    public function getNextNumber(WorkOrder $entity)
    {
        $account = $entity->account;
        $settings = $this->getSettings($entity->account);

        if($settings) {
            $counter = $settings->work_order_number_counter;
            $prefix = $settings->work_order_number_prefix ?: '';
            $counterOffset = 0;
            $numberExists = false;
            $lastNumber = false;

            do {
                $number = $prefix . str_pad($counter, $account->invoice_number_padding, '0', STR_PAD_LEFT);

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

    /**
     * @param $entity
     * @param mixed $counter
     *
     * @return bool|mixed
     */
    public function applyNumberPattern(WorkOrder $workorder, $counter = 0)
    {
        $settings = $this->getSettings($workorder->account);
        $counter = $counter ?: $settings->work_order_number_counter;
        $pattern = $settings->work_order_number_pattern;

        if (! $pattern) {
            return false;
        }

        $search = ['{$year}'];
        $replace = [date('Y')];

        $search[] = '{$counter}';
        $replace[] = str_pad($counter, $this->invoice_number_padding, '0', STR_PAD_LEFT);

        if (strstr($pattern, '{$userId}')) {
            $userId = $workorder->user ? $workorder->user->public_id : (Auth::check() ? Auth::user()->public_id : 0);
            $search[] = '{$userId}';
            $replace[] = str_pad(($userId + 1), 2, '0', STR_PAD_LEFT);
        }

        $matches = false;
        preg_match('/{\$date:(.*?)}/', $pattern, $matches);
        if (count($matches) > 1) {
            $format = $matches[1];
            $search[] = $matches[0];
            //$date = date_create()->format($format);
            $date = Carbon::now(session(SESSION_TIMEZONE, DEFAULT_TIMEZONE))->format($format);
            $replace[] = str_replace($format, $date, $matches[1]);
        }

        $pattern = str_replace($search, $replace, $pattern);
        $pattern = $this->getClientWorkOrderNumber($pattern, $workorder);

        return $pattern;
    }

        /**
     * @param $pattern
     * @param $invoice
     *
     * @return mixed
     */
    private function getClientWorkOrderNumber($pattern, $workorder)
    {
        if (! $workorder>client_id) {
            return $pattern;
        }

        $search = [
            '{$custom1}',
            '{$custom2}',
            '{$idNumber}',
            '{$clientCustom1}',
            '{$clientCustom2}',
            '{$clientIdNumber}',
            '{$clientCounter}',
        ];

        $client = $workorder->client;

        $replace = [
            $client->custom_value1,
            $client->custom_value2,
            $client->id_number,
            $client->custom_value1, // backwards compatibility
            $client->custom_value2,
            $client->id_number,
        ];

        return str_replace($search, $replace, $pattern);
    }

    public function getIntakeForm(WorkOrder $workOrder)
    {
        if($workOrder->intake_form) {
            return json_decode($workOrder->intake_form, true);
        } else {
            $settings = $this->getSettings($workOrder->account);
                        
            return json_decode($settings->intake_form, true);
        }

        return '';
    }

    public function getSettings(Account $account) : WorkOrderSettings {
        $settings = WorkOrderSettings::where('account_id', '=', $account->id)->first();

        if(!$settings) {
            // the account settings have not been defined yet, initialize them
            $settings = new WorkOrderSettings();

            $settings->account()->associate($account);
            $settings->work_order_number_counter = 1;

            $settings->save();

        }

        return $settings;
    }

    public function previewNextNumber() {
        $workorder = WorkOrder::createNew();

        return $this->getNextNumber($workorder);
    }
}
