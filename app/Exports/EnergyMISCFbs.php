<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents; 
use Maatwebsite\Excel\Events\AfterSheet;
use \Carbon\Carbon;
use DB;

class EnergyMISCFbs implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
    WithStyles, WithEvents
{
    protected $request; 

    function __construct($request) {
        $this->request = $request;
    } 
  
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()    
    {
        $metersQuery = DB::table('all_energy_meters')
            ->join('communities', 'all_energy_meters.community_id', 'communities.id')
            ->join('households', 'households.id', 'all_energy_meters.household_id')
            ->leftJoin('household_statuses', 'households.household_status_id', 'household_statuses.id')
            ->leftJoin('meter_cases', 'all_energy_meters.meter_case_id', 'meter_cases.id')
            ->leftJoin('all_energy_meter_donors', 'all_energy_meters.id','all_energy_meter_donors.all_energy_meter_id')
            ->leftJoin('donors', 'all_energy_meter_donors.donor_id', 'donors.id')
            ->where('all_energy_meters.is_archived', 0)
            ->where('all_energy_meters.energy_system_type_id', 2) 
            ->where('all_energy_meters.installation_type_id', 2)
            ->where('communities.community_status_id', "!=", 1)
            ->where(function($q) {
                $q->orWhere('households.household_status_id', 3) // AC Completed
                ->orWhere(function($q2) { // Served, no meter
                    $q2->where('households.household_status_id', 14)
                        ->whereNotNull('all_energy_meters.energy_system_cycle_id');
                })
                ->orWhere(function($q3) { // Served
                    $q3->where('households.household_status_id', 4)
                        ->whereNotNull('all_energy_meters.energy_system_cycle_id')
                        ->where('all_energy_meters.meter_active', 'Yes');
                });
            })
            ->select( 
                'households.english_name as household',
                'communities.english_name as community', 
                'household_statuses.status as status', 
                'all_energy_meters.meter_number', 
                'meter_cases.meter_case_name_english as meter_case', 'all_energy_meters.meter_active', 
                'all_energy_meters.installation_date', 'all_energy_meters.daily_limit',
                DB::raw('CASE WHEN households.number_of_male IS NULL 
                        OR households.number_of_female IS NULL 
                        OR households.number_of_adults IS NULL 
                        OR households.number_of_children IS NULL 
                    THEN "Missing Details" 
                    ELSE "Complete" 
                    END as details_status'),

                'households.number_of_male', 'households.number_of_female', 
                'households.number_of_adults', 'households.number_of_children', 
                DB::raw('CASE 
                    WHEN (households.number_of_male IS NOT NULL AND households.number_of_female IS NOT NULL 
                        AND households.number_of_adults IS NOT NULL AND households.number_of_children IS NOT NULL 
                        AND (households.number_of_adults + households.number_of_children) <> (households.number_of_male + households.number_of_female))
                    THEN "Discrepancy" 
                    ELSE "No Discrepancy" 
                    END as discrepancies_status'),
                'households.phone_number',
                DB::raw('group_concat(DISTINCT CASE WHEN all_energy_meter_donors.is_archived = 0 
                    THEN donors.donor_name END) as donors'),
                DB::raw('NULL as notes'),
                DB::raw('"Metered Household" as source')
            )
            ->groupBy('all_energy_meters.id');

           
        $householdsQuery = DB::table('households')
            ->join('communities', 'households.community_id', 'communities.id')
            ->where('households.is_archived', 0)
            ->where('households.energy_system_type_id', 2)
            ->where('households.household_status_id', 11)
            ->where('communities.energy_system_cycle_id', null)
            ->where('communities.community_status_id', "!=", 1)
            ->select(
                'households.english_name as household',
                'communities.english_name as community',
                DB::raw('"Confirmed" as status'),
                DB::raw('NULL as meter_number'),
                DB::raw('NULL as meter_case'),
                DB::raw('NULL as meter_active'),
                DB::raw('NULL as installation_date'),
                DB::raw('NULL as daily_limit'),

                DB::raw('CASE WHEN households.number_of_male IS NULL
                    OR households.number_of_female IS NULL
                    OR households.number_of_adults IS NULL
                    OR households.number_of_children IS NULL
                    THEN "Missing Details" ELSE "Complete" END as details_status'),

                'households.number_of_male',
                'households.number_of_female',
                'households.number_of_adults',
                'households.number_of_children',

                DB::raw('"No Discrepancy" as discrepancies_status'),
                'households.phone_number',

                DB::raw('NULL as donors'),
                'households.confirmation_notes as notes',
                DB::raw('"Confirmed Household" as source')
            );


 
        if($this->request->community_id) {

            $metersQuery->where("communities.id", $this->request->community_id);
            $householdsQuery->where("communities.id", $this->request->community_id);
        }

        if($this->request->energy_cycle_id) {

            $metersQuery->where("all_energy_meters.energy_system_cycle_id", $this->request->energy_cycle_id);
            $householdsQuery->where("households.energy_system_cycle_id", $this->request->energy_cycle_id);
        } 

        return $metersQuery->unionAll($householdsQuery)->get();
    } 

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["Household", "Community", "Household Status", "Meter Number", "Meter Case", "Meter Active", 
            "Installation Date", "Daily Limit", "All Details", "Number of male", "Number of Female", "Number of adults", 
            "Number of children", "Discrepancy", "Phone number", "Donors", "Notes", "Source"];
    }
 
    public function title(): string
    {
        return 'MISC FBS';
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
              
                $event->sheet->getDelegate()->freezePane('A2');  
            },
        ];
    }

    /**
     * Styling
     *
     * @return response()
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:R1');

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}