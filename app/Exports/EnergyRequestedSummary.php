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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use \Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\HouseholdStatus;
use App\Models\Household;
use DB;

class EnergyRequestedSummary implements
    FromCollection,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithEvents,
    WithCustomStartCell
{
    private  $miscHouseholds = 0, $miscAcCompleted = 0, $activeNoMeterFBS = 0, $activateMisc = 0, 
        $requestedHouseholds = 0, $miscRefrigerator = 0, $fbsUpgradeMGSMG = 0, 
        $miscHouseholdsFBS = 0, $miscHouseholdsMGSMG = 0, $activeNoMeterMG = 0, 
        
        $relocatedConfirmed = [], $relocatedActiveNoMeter = [], $relocatedActiveWithMeter = [], $relocatedRefrigerator;


    protected $request;

    function __construct($request)
    {

        $this->request = $request;
    }


    /**
     * Base query for relocated confirmed households
     */
    private function relocatedConfirmedBaseQuery($energyCycleId = null)
    {
        $query = DB::table('displaced_households')
            ->join('households', 'displaced_households.household_id', '=', 'households.id')
            ->where('displaced_households.is_archived', 0)
            ->where('households.is_archived', 0)
            ->where('displaced_households.displaced_household_status_id', 3)
            ->where('households.household_status_id', 11);

        if (!empty($energyCycleId)) $query->where('households.energy_system_cycle_id', $energyCycleId);

        return $query;
    }


    /**
     * Base query for relocated active – no meter & with meter 
     */
    private function relocatedSheetBaseQuery($energyCycleId = null)
    {
        $query = DB::table('displaced_households')
            ->join('all_energy_meters', 'all_energy_meters.household_id', '=', 'displaced_households.household_id')
            ->join('households', 'households.id', '=', 'displaced_households.household_id')
            ->where('displaced_households.is_archived', 0)
            ->where('all_energy_meters.is_archived', 0)
            ->where('households.is_archived', 0);

        if ($energyCycleId) {
            $query->where('all_energy_meters.energy_system_cycle_id', $energyCycleId);
        }

        return $query;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // FBS Upgrade to SMG (new families)
        $this->fbsUpgradeMGSMG = DB::table('all_energy_meters')
            ->join('households', 'all_energy_meters.household_id', 'households.id')
            ->join('household_meters', 'all_energy_meters.id', 'household_meters.energy_user_id')
            ->where('household_meters.is_archived', 0)
            ->where('all_energy_meters.is_archived', 0)
            ->where('household_meters.fbs_upgrade_new', 1);

        // MISC FBS Households "Confirmed" 
        $this->miscHouseholds = DB::table('households')
            ->join('communities', 'households.community_id', 'communities.id')
            ->where('households.is_archived', 0)
            ->where('households.energy_system_type_id', 2)
            ->where('households.household_status_id', 11)
            ->where('communities.energy_system_cycle_id', null);


        $cycleId = $this->request->energy_cycle_id ?? null;

        $queryFBS = DB::table('all_energy_meters')
            ->join('households', 'households.id', '=', 'all_energy_meters.household_id')
            ->join('communities', 'communities.id', '=', 'households.community_id')
            ->where('all_energy_meters.is_archived', 0)
            ->where('all_energy_meters.energy_system_type_id', 2)
            ->where('all_energy_meters.installation_type_id', 2)
            ->where('communities.community_status_id', "!=", 1)
            ->when($cycleId, function ($q) use ($cycleId) {
                $q->where('all_energy_meters.energy_system_cycle_id', $cycleId);
            })
            ->selectRaw("
                COUNT(CASE 
                    WHEN households.household_status_id = 3 
                    THEN 1 END) AS ac_completed,

                COUNT(CASE 
                    WHEN households.household_status_id = 14
                    AND all_energy_meters.energy_system_cycle_id IS NOT NULL
                    THEN 1 END) AS served_no_meter,

                COUNT(CASE 
                    WHEN households.household_status_id = 4
                    AND all_energy_meters.energy_system_cycle_id IS NOT NULL
                    AND all_energy_meters.meter_active = 'Yes'
                    THEN 1 END) AS served
            ")
            ->first();


        // MISC FBS "AC Completed" 
        $this->miscAcCompleted = $queryFBS->ac_completed;

        // MISC FBS "Served, no meter"
        $this->activeNoMeterFBS = $queryFBS->served_no_meter;
        
        // MISC FBS "Served"
        $this->activateMisc = $queryFBS->served;


        // MISC FBS Refrigerator
        $this->miscRefrigerator = DB::table('refrigerator_holders')
            ->join('households', 'households.id', 'refrigerator_holders.household_id')
            ->join('all_energy_meters', 'all_energy_meters.household_id', 'households.id')
            ->where('all_energy_meters.energy_system_cycle_id', '!=', NULL)
            ->where('refrigerator_holders.is_archived', 0)
            ->where('all_energy_meters.is_archived', 0)
            ->where('households.is_archived', 0)
            ->where('households.household_status_id', 4)
            ->where('all_energy_meters.energy_system_type_id', 2)
            ->where('all_energy_meters.installation_type_id', 2)
            ->where('all_energy_meters.meter_active', 'Yes');


        $this->activeNoMeterMG = DB::table('households')
            ->leftJoin('all_energy_meters', 'all_energy_meters.household_id', 'households.id')
            ->leftJoin('communities', 'households.community_id', 'communities.id')
            ->where('households.is_archived', 0)
            ->where('households.household_status_id', 14)
            ->where(function ($q) {
                $q->whereNull('all_energy_meters.id')
                    ->orWhere('all_energy_meters.meter_number', 0)
                    ->orWhere('all_energy_meters.meter_active', 'No');
            })
            ->where(function ($q) {
                $q->where('households.energy_system_type_id', 1)
                    ->orWhere('all_energy_meters.energy_system_type_id', 1);
            })
        ;
        
        // Requested
        $this->requestedHouseholds = DB::table('households')
            ->join('energy_request_systems', 'energy_request_systems.household_id', 'households.id')
            ->join('communities', 'households.community_id', 'communities.id')
            //->where('households.energy_system_cycle_id',  $cycleYear)
            ->where('households.is_archived', 0)
            ->where('households.household_status_id', 5);


        // Used for many variables
        $energySystemTypes = [
            'fbs' => 2,
            'mg'  => 1,
            'smg' => 4,
        ];

        $this->relocatedConfirmed = [];

        foreach ($energySystemTypes as $key => $energyTypeId) {

            $this->relocatedConfirmed[$key] = (clone $this->relocatedConfirmedBaseQuery())
                ->where('households.energy_system_type_id', $energyTypeId)
                ->count();
        }

        $this->relocatedActiveNoMeter = [
            'fbs' => (clone $this->relocatedSheetBaseQuery($this->request->energy_cycle_id))
                ->where('households.household_status_id', 14)
                ->where('all_energy_meters.energy_system_type_id', 2)
                ->count(),

            'mg' => (clone $this->relocatedSheetBaseQuery($this->request->energy_cycle_id))
                ->where('households.household_status_id', 14)
                ->where('all_energy_meters.energy_system_type_id', 1)
                ->count(),

            'smg' => (clone $this->relocatedSheetBaseQuery($this->request->energy_cycle_id))
                ->where('households.household_status_id', 14)
                ->where('all_energy_meters.energy_system_type_id', 4)
                ->count(),
        ];

       // dd($this->relocatedActiveNoMeter);

        $this->relocatedActiveWithMeter = [
            'fbs' => (clone $this->relocatedSheetBaseQuery($this->request->energy_cycle_id))
                ->where('households.household_status_id', 4)
                ->where('all_energy_meters.meter_case_id', 1)
                ->distinct('displaced_households.id')
                ->where('all_energy_meters.energy_system_type_id', 2)
                ->count(),

            'mg' => (clone $this->relocatedSheetBaseQuery($this->request->energy_cycle_id))
                ->where('households.household_status_id', 4)
                ->where('all_energy_meters.meter_case_id', 1)
                ->distinct('displaced_households.id')
                ->where('all_energy_meters.energy_system_type_id', 1)
                ->count(),

            'smg' => (clone $this->relocatedSheetBaseQuery($this->request->energy_cycle_id))
                ->where('households.household_status_id', 4)
                ->where('all_energy_meters.meter_case_id', 1)
                ->where('all_energy_meters.energy_system_type_id', 4)
                ->distinct('displaced_households.id')
                ->count(),
        ];


        // Relocated Refrigerator
        $this->relocatedRefrigerator = DB::table('refrigerator_holders')
            ->join('households', 'refrigerator_holders.household_id', 'households.id')
            ->join('displaced_households', 'households.id', 'displaced_households.household_id')
            ->join('all_energy_meters', 'all_energy_meters.household_id', 'households.id')
            ->where('all_energy_meters.is_archived', 0)
            ->where('refrigerator_holders.is_archived', 0)
            ->where('all_energy_meters.energy_system_cycle_id', '!=', NULL);


        $householdStatus = HouseholdStatus::where('status', "On Hold")->first();

        $queryCommunities = DB::table('communities')
            ->join('regions', 'communities.region_id', 'regions.id')
            ->join('community_statuses', 'communities.community_status_id', 'community_statuses.id')
            ->leftJoin('households as all_households', 'all_households.community_id', 'communities.id')
            ->leftJoin('energy_system_types as all_energy_types', 'all_energy_types.id', 'all_households.energy_system_type_id')
            ->leftJoin('all_energy_meters', 'all_energy_meters.household_id', 'all_households.id')
            ->leftJoin('grid_community_compounds', 'communities.id', 'grid_community_compounds.community_id')
            ->leftJoin('public_structures', 'public_structures.community_id', 'communities.id')

            ->leftJoin('refrigerator_holders', 'all_households.id', 'refrigerator_holders.household_id')
            ->leftJoin('community_donors', function ($join) {
                $join->on('communities.id', 'community_donors.community_id')
                    ->where('community_donors.is_archived', 0);
            })
            ->leftJoin('donors', 'community_donors.donor_id', 'donors.id')
            ->where('communities.is_archived', 0)
            ->where('all_households.is_archived', 0)
            //->where('communities.energy_system_cycle_id', $cycleYear)
            ->where('all_households.household_status_id', '!=', $householdStatus->id)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('compounds')
                    ->whereRaw('compounds.community_id = communities.id');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('displaced_households')
                    ->whereRaw('displaced_households.household_id = all_households.id');
            })
            ->select(
                'communities.english_name',
                'regions.english_name as region',
                DB::raw('false as unneeded'),
                DB::raw('COUNT(CASE WHEN all_energy_types.id = 2 THEN 1 END) + 
                    COUNT(CASE WHEN public_structures.is_archived = 0 AND public_structures.energy_system_type_id = 2 THEN 1 END)
                    as sum_FBS'),
                DB::raw('COUNT(CASE WHEN all_energy_types.id = 1 THEN 1 END)  + 
                    COUNT(CASE WHEN public_structures.is_archived = 0 AND public_structures.energy_system_type_id = 1 THEN 1 END)
                    as sum_MG'),
                DB::raw('COUNT(CASE WHEN all_energy_types.id = 4 THEN 1 END)  + 
                    COUNT(CASE WHEN public_structures.is_archived = 0 AND public_structures.energy_system_type_id = 4 THEN 1 END)
                    as sum_SMG'),
                'grid_community_compounds.electricity_room',
                'grid_community_compounds.grid',
                DB::raw('COUNT(CASE WHEN all_households.is_archived = 0 AND all_households.household_status_id = 1 THEN 1 END) as sum_inital'),
                DB::raw('COUNT(CASE WHEN all_households.household_status_id = 3 THEN 1 END) as sum_AC'),

                // Active No Meter Mg: energy_system_type_id != 2 and meter_number = 0
                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_number = 0 AND 
                    all_energy_meters.energy_system_type_id != 2 THEN 1 END) as active_no_meter_MG'),

                // Active No Meter FBS: energy_system_type_id = 2 and meter_number = 0
                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_number = 0 AND 
                    all_energy_meters.energy_system_type_id = 2 AND all_households.household_status_id = 14 THEN 1 END) 
                    as active_no_meter_FBS'),

                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_active = "Yes" AND 
                    all_energy_meters.energy_system_type_id != 2 AND all_households.household_status_id = 4 THEN 1 END) as sum_DC_MG'),

                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_active = "Yes" AND 
                    all_energy_meters.energy_system_type_id = 2 THEN 1 END) as sum_DC_FBS'),

                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.is_main = "No" THEN 1 END) 
                    as sum_shared_household'),

                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.household_id = NULL AND 
                    all_energy_meters.meter_active = "Yes" AND all_energy_meters.energy_system_type_id != 2 THEN 1 END) as sum_public_MG'),
                
                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.household_id = NULL AND 
                    all_energy_meters.meter_active = "Yes" AND all_energy_meters.energy_system_type_id = 2 THEN 1 END) as sum_public_FBS'),

                DB::raw('false as unneeded'),
                DB::raw('false as unneeded'),
                DB::raw('COUNT(DISTINCT CASE 
                    WHEN refrigerator_holders.id IS NOT NULL AND all_households.is_archived = 0 
                    THEN all_households.id  END) as total_refrigerators'
                ),
                DB::raw('group_concat(DISTINCT donors.donor_name) as donors'),

            )
            ->groupBy('communities.english_name');

        
        $queryCompounds = DB::table('compounds')
            ->join('communities', 'communities.id', 'compounds.community_id')
            ->join('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('compound_households', 'compound_households.compound_id', 'compounds.id')
            ->leftJoin('households', 'compound_households.household_id', 'households.id')
            ->leftJoin('energy_system_types', 'households.energy_system_type_id', 'energy_system_types.id')
            ->leftJoin('all_energy_meters', 'all_energy_meters.household_id', 'households.id')
            ->leftJoin('grid_community_compounds', 'compounds.id', 'grid_community_compounds.compound_id')
            ->leftJoin('public_structures', 'public_structures.compound_id', 'compounds.id')
            ->leftJoin('all_energy_meters as public_meters', 'public_meters.public_structure_id', 'public_structures.id')
            ->leftJoin('refrigerator_holders', 'households.id', 'refrigerator_holders.household_id')
            ->leftJoin('community_donors', function ($join) {
                $join->on('compounds.id', 'community_donors.compound_id')
                    ->where('community_donors.is_archived', 0);
            })
            ->leftJoin('donors', 'community_donors.donor_id', 'donors.id')
            ->where('communities.is_archived', 0)
            ->where('compounds.is_archived', 0)
            ->where('households.is_archived', 0)
            //->where('compound_households.is_archived', 0)
            //->where('communities.energy_system_cycle_id', $cycleYear)
            ->where('households.household_status_id', '!=', $householdStatus->id)
            ->select(
                'compounds.english_name',
                'regions.english_name as region',
                DB::raw('false as unneeded'),
                DB::raw('COUNT(DISTINCT CASE WHEN households.is_archived = 0 AND households.energy_system_type_id = 2 
                    THEN households.id END) + 
                    COUNT(DISTINCT CASE WHEN public_structures.is_archived = 0 AND public_structures.energy_system_type_id = 2 
                    THEN public_structures.id END) as sum_FBS'),
                DB::raw('COUNT(DISTINCT CASE WHEN households.is_archived = 0 AND households.energy_system_type_id = 1 
                    THEN households.id END) + 
                    COUNT(DISTINCT CASE WHEN public_structures.is_archived = 0 AND public_structures.energy_system_type_id = 1 
                    THEN public_structures.id END) as sum_MG'),
                DB::raw('COUNT(DISTINCT CASE WHEN households.is_archived = 0 AND households.energy_system_type_id = 4 THEN 
                    households.id END) +
                    COUNT(DISTINCT CASE WHEN public_structures.is_archived = 0 AND public_structures.energy_system_type_id = 4 
                    THEN public_structures.id END) as sum_SMG'),
                'grid_community_compounds.electricity_room',
                'grid_community_compounds.grid',
                DB::raw('COUNT(CASE WHEN households.is_archived = 0 AND households.household_status_id = 1 THEN 1 END) as sum_inital'),
                DB::raw('COUNT(CASE WHEN households.is_archived = 0 AND households.household_status_id = 3 THEN households.id END) as sum_AC'),
                // Active No Meter Mg: energy_system_type_id != 2 and meter_number = 0
                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_number = 0 AND all_energy_meters.energy_system_type_id != 2 
                    AND households.household_status_id = 14 THEN 1 END) as active_no_meter_MG'),
                // Active No Meter FBS: energy_system_type_id = 2 and meter_number = 0
                DB::raw('COUNT(CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_number = 0 AND all_energy_meters.energy_system_type_id = 2 AND 
                    households.household_status_id = 14 THEN 1 END) as active_no_meter_FBS'),
                DB::raw('COUNT(DISTINCT CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_active = "Yes" AND 
                    all_energy_meters.energy_system_type_id != 2 THEN households.id END) as sum_DC_MG'),
                DB::raw('COUNT(DISTINCT CASE WHEN all_energy_meters.is_archived = 0 AND all_energy_meters.meter_active = "Yes" AND all_energy_meters.energy_system_type_id = 2 
                    THEN households.id END) as sum_DC_FBS'),
                DB::raw('COUNT(CASE WHEN households.is_archived = 0 AND all_energy_meters.is_main = "No" THEN 1 END) as sum_shared_household'),

                DB::raw('COUNT(DISTINCT CASE WHEN public_meters.meter_active = "Yes" AND public_meters.energy_system_type_id != 2 
                    THEN public_structures.id END) as sum_public_MG'),
                DB::raw('COUNT(DISTINCT CASE WHEN public_meters.meter_active = "Yes" AND public_meters.energy_system_type_id = 2 
                    THEN public_structures.id END) as sum_public_FBS'),

                DB::raw('false as unneeded'),
                DB::raw('false as unneeded'),

                DB::raw('COUNT(DISTINCT CASE 
                    WHEN refrigerator_holders.id IS NOT NULL AND households.is_archived = 0 
                    THEN households.id  END) as total_refrigerators'
                ),
                DB::raw('group_concat(DISTINCT donors.donor_name) as donors'),
            )
            ->groupBy('compounds.english_name');

        if ($this->request->community_id) {

            $queryCompounds->where("communities.id", $this->request->community_id);
        }
        if ($this->request->request_status) {

            $queryCompounds->where("energy_request_systems.energy_request_status_id", $this->request->request_status);
        }
        if ($this->request->energy_cycle_id) {

            $queryCommunities->where("communities.energy_system_cycle_id", $this->request->energy_cycle_id);
            $queryCompounds->where("compounds.energy_system_cycle_id", $this->request->energy_cycle_id);

            // MISC FBS
            $this->miscHouseholds->where("households.energy_system_cycle_id", $this->request->energy_cycle_id);

            // FBS Upgrade
            $this->fbsUpgradeMGSMG->where("households.energy_system_cycle_id", $this->request->energy_cycle_id);

            // ensure active-no-meter queries are assigned to the requested cycle
            $this->activeNoMeterMG->where('communities.energy_system_cycle_id', $this->request->energy_cycle_id);


            $cycleId = (int) $this->request->energy_cycle_id;

            if ($cycleId > 0) {

                $baseYear = 2023;
                $startYear = $baseYear + ($cycleId - 1);
                $endYear = $startYear + 1;

                $startDate = "$startYear-04-01"; // April 1st
                $endDate = "$endYear-03-31"; // March 31st of next year

                $this->relocatedRefrigerator->where("all_energy_meters.energy_system_cycle_id", $this->request->energy_cycle_id)
                    ->whereBetween('refrigerator_holders.date', [$startDate, $endDate]);

                $this->miscRefrigerator->where("households.energy_system_cycle_id", $this->request->energy_cycle_id)
                    ->whereBetween('refrigerator_holders.date', [$startDate, $endDate]);
            }
        } else {
            // default to NULL cycles when no specific cycle requested
            $this->activeNoMeterMG->whereNull('communities.energy_system_cycle_id');
            $this->activeNoMeterFBS->whereNull('communities.energy_system_cycle_id');
        }



        // MISC
        $this->miscHouseholds = $this->miscHouseholds->count();
        $this->miscRefrigerator = $this->miscRefrigerator->count();

        // FBS Upgrades
        $this->fbsUpgradeMGSMG = $this->fbsUpgradeMGSMG->count();

        //$this->requestedHouseholds = $this->requestedHouseholds->count();

        // Relocated
        $this->relocatedRefrigerator = $this->relocatedRefrigerator->count();

        $this->activeNoMeterMG = $this->activeNoMeterMG->count();

        $communitiesCollection = $queryCommunities->get();
        $compoundsCollection   = $queryCompounds->get();

        return $compoundsCollection->merge($communitiesCollection);
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function title(): string
    {
        return 'Energy Progress Summary';
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

                $event->sheet->getDelegate()->freezePane('A1');
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
        $sheet->setAutoFilter('A1:U1');
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Geographical Region');
        $sheet->setCellValue('C1', 'FBS to SMG New Confirmed Households');
        $sheet->setCellValue('D1', 'FBS # confirmed meters');
        $sheet->setCellValue('E1', 'MG # confirmed meters');
        $sheet->setCellValue('F1', 'SMG # confirmed meters');
        $sheet->setCellValue('G1', 'Electricity Room');
        $sheet->setCellValue('H1', 'Grid');
        $sheet->setCellValue('I1', 'Initial Households/Public');
        $sheet->setCellValue('J1', 'Completed AC');
        $sheet->setCellValue('K1', 'Active NO Meter MG'); //New Column
        $sheet->setCellValue('L1', 'Active NO Meter FBS');//New Column
        $sheet->setCellValue('M1', 'Activate Meter MG'); // household_status is in-progress
        $sheet->setCellValue('N1', 'Activate Meter FBS'); // household_status is served// MG
        $sheet->setCellValue('O1', 'Shared Households');
        $sheet->setCellValue('P1', 'Public Structures MG');
        $sheet->setCellValue('Q1', 'Public Structures FBS');
        $sheet->setCellValue('R1', 'Served');
        $sheet->setCellValue('S1', 'Delta');
        $sheet->setCellValue('T1', 'Refrigerator');
        $sheet->setCellValue('U1', 'Donors');

        $sheet->setCellValue('A2', 'MISC FBS');
        $sheet->setCellValue('A3', 'Relocated Households');
        $sheet->setCellValue('A4', 'FBS Upgarade to MG/SMG');
    
        $sheet->setCellValue('B2', ' ');
        $sheet->setCellValue('B3', ' ');
        $sheet->setCellValue('B4', ' ');

        // MISC FBS
        $sheet->setCellValue('D2', $this->miscHouseholds + $this->miscAcCompleted + $this->activeNoMeterFBS
            + $this->activateMisc); // MISC FBS Confirmed Households
        $sheet->setCellValue('J2', $this->miscAcCompleted); // MISC FBS AC Completed
        $sheet->setCellValue('L2', $this->activeNoMeterFBS); // MISC FBS Served, no meter
        $sheet->setCellValue('N2', $this->activateMisc); // MISC FBS Served
        $sheet->setCellValue('R2', ($this->activateMisc + $this->activeNoMeterFBS)); // MISC Active
        $sheet->setCellValue('S2', ($this->miscHouseholds + $this->miscAcCompleted + $this->activeNoMeterFBS
            + $this->activateMisc) - ($this->activeNoMeterFBS + $this->activateMisc)); // MISC FBS Delta
        $sheet->setCellValue('T2', ($this->miscRefrigerator)); // MISC FBS Refrigerator

    
        
        // Relocated
        $types = ['fbs', 'mg', 'smg'];

        $sum = function ($array) use ($types) {
            return array_sum(array_intersect_key($array, array_flip($types)));
        };

        $excelRelocatedCells = [
            'D3' => array_sum(array_map(fn($t) =>
                $this->relocatedConfirmed[$t]
                + $this->relocatedActiveNoMeter[$t]
                + $this->relocatedActiveWithMeter[$t], ['fbs'])),

            'E3' => array_sum(array_map(fn($t) =>
                $this->relocatedConfirmed[$t]
                + $this->relocatedActiveNoMeter[$t]
                + $this->relocatedActiveWithMeter[$t], ['mg'])),

            'F3' => array_sum(array_map(fn($t) =>
                $this->relocatedConfirmed[$t]
                + $this->relocatedActiveNoMeter[$t]
                + $this->relocatedActiveWithMeter[$t], ['smg'])),

            'K3' => $this->relocatedActiveNoMeter['mg']
                + $this->relocatedActiveNoMeter['smg'],

            'L3' => $this->relocatedActiveNoMeter['fbs'],

            'M3' => $this->relocatedActiveWithMeter['mg']
                + $this->relocatedActiveWithMeter['smg'],

            'N3' => $this->relocatedActiveWithMeter['fbs'],

            'R3' => $sum($this->relocatedActiveNoMeter)
                + $sum($this->relocatedActiveWithMeter),

            'S3' => (
                    array_sum(array_map(fn($t) =>
                    $this->relocatedConfirmed[$t]
                    + $this->relocatedActiveNoMeter[$t]
                    + $this->relocatedActiveWithMeter[$t], ['smg']))  
                +
                    array_sum(array_map(fn($t) =>
                    $this->relocatedConfirmed[$t]
                    + $this->relocatedActiveNoMeter[$t]
                    + $this->relocatedActiveWithMeter[$t], ['mg']))
                +
                    array_sum(array_map(fn($t) =>
                    $this->relocatedConfirmed[$t]
                    + $this->relocatedActiveNoMeter[$t]
                    + $this->relocatedActiveWithMeter[$t], ['fbs']))
                ) -
                    ($sum($this->relocatedActiveNoMeter)
                    +  $sum($this->relocatedActiveWithMeter)),



            'T3' => $this->relocatedRefrigerator,
        ];

      
        foreach ($excelRelocatedCells as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Needs to be checked soon ... when we have confirmed SMG
        $sheet->setCellValue('C4', $this->fbsUpgradeMGSMG);
        $sheet->setCellValue('R4', $this->fbsUpgradeMGSMG);
        $sheet->setCellValue('S4', 0);


        
        $sheet->setCellValue('T4', ($this->relocatedRefrigerator));


        // The code here for adding the values for Total Served and Delta
        $rowServed = 5; 
        $endRow = $sheet->getHighestRow(); 

        for ($r = $rowServed; $r <= $endRow; $r++) {
            $sheet->setCellValue(
                'R' . $r,
                '=SUM(K' . $r . ':Q' . $r . ')'
            );

            $sheet->setCellValue(
                'S' . $r,
                '=(D' . $r . '+E' . $r . '+F' . $r . ')-R' . $r
            );
        }


        // Adding the summation row
        $lastRow = $sheet->getHighestRow() + 1;
        $sheet->setCellValue('A' . $lastRow, 'Total');
        $sheet->setCellValue('C' . $lastRow, '=SUM(C2:C' . ($lastRow - 1) . ')');
        $sheet->setCellValue('D' . $lastRow, '=SUM(D2:D' . ($lastRow - 1) . ')');
        $sheet->setCellValue('E' . $lastRow, '=SUM(E2:E' . ($lastRow - 1) . ')');
        $sheet->setCellValue('F' . $lastRow, '=SUM(F2:F' . ($lastRow - 1) . ')');
        $sheet->setCellValue('I' . $lastRow, '=SUM(I2:I' . ($lastRow - 1) . ')');
        $sheet->setCellValue('J' . $lastRow, '=SUM(J2:J' . ($lastRow - 1) . ')');
        $sheet->setCellValue('M' . $lastRow, '=SUM(M2:M' . ($lastRow - 1) . ')');
        $sheet->setCellValue('N' . $lastRow, '=SUM(N2:N' . ($lastRow - 1) . ')');
        $sheet->setCellValue('O' . $lastRow, '=SUM(O2:O' . ($lastRow - 1) . ')');
        $sheet->setCellValue('P' . $lastRow, '=SUM(P2:P' . ($lastRow - 1) . ')');
        $sheet->setCellValue('Q' . $lastRow, '=SUM(Q2:Q' . ($lastRow - 1) . ')');
        $sheet->setCellValue('R' . $lastRow, '=SUM(R2:R' . ($lastRow - 1) . ')');
        $sheet->setCellValue('S' . $lastRow, '=SUM(S2:S' . ($lastRow - 1) . ')');
        $sheet->setCellValue('T' . $lastRow, '=SUM(T2:T' . ($lastRow - 1) . ')');
        // Total for Active No Meter (MG + FBS) merged into K:L
        $sheet->setCellValue('K' . $lastRow, '=SUM(K2:K' . ($lastRow - 1) . ')+SUM(L2:L' . ($lastRow - 1) . ')');
        $sheet->mergeCells('K' . $lastRow . ':L' . $lastRow);
        $sheet->setCellValue('U' . $lastRow, '');

        // Confirmed 
        $sheet->getStyle('D1:D' . ($lastRow - 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('D1:D' . ($lastRow - 1))->getFill()->setStartColor(new Color('ADD8E6'));
        $sheet->getStyle('D1:D' . ($lastRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('D1:D' . ($lastRow - 1))->getBorders()->getAllBorders()->setColor(new Color('000000'));

        $sheet->getStyle('E1:E' . ($lastRow - 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('E1:E' . ($lastRow - 1))->getFill()->setStartColor(new Color('ADD8E6'));
        $sheet->getStyle('E1:E' . ($lastRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('E1:E' . ($lastRow - 1))->getBorders()->getAllBorders()->setColor(new Color('000000'));

        $sheet->getStyle('F1:F' . ($lastRow - 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('F1:F' . ($lastRow - 1))->getFill()->setStartColor(new Color('ADD8E6'));
        $sheet->getStyle('F1:F' . ($lastRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('F1:F' . ($lastRow - 1))->getBorders()->getAllBorders()->setColor(new Color('000000'));

        // Initial
        $sheet->getStyle('I1:I' . ($lastRow - 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('I1:I' . ($lastRow - 1))->getFill()->setStartColor(new Color('e6e6ff'));
        $sheet->getStyle('I1:I' . ($lastRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('I1:I' . ($lastRow - 1))->getBorders()->getAllBorders()->setColor(new Color('000000'));

        // AC Completed
        $sheet->getStyle('J1:J' . ($lastRow - 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('J1:J' . ($lastRow - 1))->getFill()->setStartColor(new Color('e6e600'));
        $sheet->getStyle('J1:J' . ($lastRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('J1:J' . ($lastRow - 1))->getBorders()->getAllBorders()->setColor(new Color('000000'));

        // Served
        $sheet->getStyle('R1:R' . ($lastRow - 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('R1:R' . ($lastRow - 1))->getFill()->setStartColor(new Color('86af49'));
        $sheet->getStyle('R1:R' . ($lastRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('R1:R' . ($lastRow - 1))->getBorders()->getAllBorders()->setColor(new Color('000000'));

        // Delta
        $sheet->getStyle('S1:S' . ($lastRow - 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('S1:S' . ($lastRow - 1))->getFill()->setStartColor(new Color('e60000'));
        $sheet->getStyle('S1:S' . ($lastRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('S1:S' . ($lastRow - 1))->getBorders()->getAllBorders()->setColor(new Color('000000'));

        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true, 'size' => 12]],
            // Optionally, you can style the total row as well
            $lastRow => ['font' => ['bold' => true, 'size' => 12]]
        ];
    }

}