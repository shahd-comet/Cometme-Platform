<?php

namespace App\Imports;

use App\Models\WaterQualityResult;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\User;
use App\Models\AllEnergyMeter;
use App\Models\AllEnergyMeterHistoryCase;
use App\Models\DeactivatedEnergyHolder;
use App\Models\Household;
use Carbon\Carbon;
use Excel; 

class ImportReactivatedHolder implements ToModel, WithHeadingRow
{ 
    /**  
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Get data from KOBO 
        if($row["select_community"]) { 

            $reactivationDate = null;
            $household = Household::where("comet_id", $row["select_household_name"])->first();
            $allEnergyMeter = AllEnergyMeter::where("is_archived", 0)->where("household_id", $household->id)->first();

            $visitDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['workshop_date']);
            if($row['reactivation_date']) $reactivationDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['reactivation_date']);
         
            if($allEnergyMeter) {

                $rawValue = strtolower($row["submitted_by"]);
                $cleanValue = preg_replace('/[0-9]+/', '', $rawValue);
                $fullName = str_replace('_', ' ', $cleanValue);
                $submittedBy = User::whereRaw('LOWER(name) LIKE ?', ["%{$fullName}%"])->first();
                if (!$submittedBy) {
                    $firstName = explode(' ', $fullName)[0];

                    $submittedBy = User::whereRaw('LOWER(name) LIKE ?', ["{$firstName}%"])->first();
                }

                $reactivatedHolder = new DeactivatedEnergyHolder(); 
                $reactivatedHolder->all_energy_meter_id = $allEnergyMeter->id;
                $reactivatedHolder->meter_number = $allEnergyMeter->meter_number;
                $reactivatedHolder->visit_date = $visitDate->format('Y-m-d');
                $reactivatedHolder->user_id = $submittedBy->id;
                $reactivatedHolder->is_paid = $row["select_fine_paid"];
                $reactivatedHolder->paid_amount = $row["amount"];
                if($row['reactivation_date']) $reactivatedHolder->reactivation_date = $reactivationDate->format('Y-m-d');
                $reactivatedHolder->deactivated_after_war = $row["select_deactivate_meter"];
                $reactivatedHolder->is_return = $row["select_reactivation"];
                $reactivatedHolder->system_status = $row["select_system_status"];
                $reactivatedHolder->notes = $row["deactivation_notes"];
                $reactivatedHolder->save();

                if($row['reactivation_date'] && $row["select_reactivation"] == "Yes") {

                    $allEnergyMeterHistoryCase = new AllEnergyMeterHistoryCase();
                    $allEnergyMeterHistoryCase->all_energy_meter = $allEnergyMeter->id;
                    $allEnergyMeterHistoryCase->old_meter_case_id = $allEnergyMeter->meter_case_id;
                    $allEnergyMeterHistoryCase->new_meter_case_id = 1;
                    $allEnergyMeterHistoryCase->last_update_date = $reactivationDate->format('Y-m-d');
                    $allEnergyMeterHistoryCase->save();

                    $allEnergyMeter->meter_case_id = 1;
                    $allEnergyMeter->save();
                }
            }
        }
    }
}