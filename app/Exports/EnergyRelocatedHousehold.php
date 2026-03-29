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

class EnergyRelocatedHousehold implements
    FromCollection, WithHeadings, WithTitle,
    ShouldAutoSize, WithStyles, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $cycleId = $this->request->energy_cycle_id;

        $displacedToTown = DB::table('displaced_households')
            ->join('households', 'households.id', 'displaced_households.household_id')
            ->join('communities as old_communities', 'displaced_households.old_community_id', 'old_communities.id')
            ->join('communities', 'households.community_id', 'communities.id')
            ->where('displaced_households.is_archived', 0)
            ->where('displaced_households.displaced_household_status_id', 1)
            ->where('households.is_archived', 0)
            ->where('households.household_status_id', 6)
            ->select(
                'households.english_name as household',
                'communities.english_name as community_name',
                'old_communities.english_name as old_community_name',
                DB::raw('"Displaced - Moved To Town" as status'),
                DB::raw('NULL as meter_number'),
                DB::raw('NULL as meter_case_name_english'),
                DB::raw('NULL as meter_active'),
                DB::raw('NULL as installation_date'),
                DB::raw('NULL as daily_limit'),

                DB::raw('CASE WHEN households.number_of_male IS NULL
                    OR households.number_of_female IS NULL
                    OR households.number_of_adults IS NULL
                    OR households.number_of_children IS NULL
                    THEN "Missing Details" ELSE "Complete" END'),

                'households.number_of_male',
                'households.number_of_female',
                'households.number_of_adults',
                'households.number_of_children',

                DB::raw('"No Discrepancy" as discrepancies_status'),
                'households.phone_number',
                DB::raw('NULL as donors')
            );

        $displaced = DB::table('displaced_households')
            ->join('households', 'households.id', 'displaced_households.household_id')
            ->join('communities as old_communities', 'displaced_households.old_community_id', 'old_communities.id')
            ->join('communities', 'households.community_id', 'communities.id')
            ->where('displaced_households.is_archived', 0)
            ->where('displaced_households.displaced_household_status_id', 4)
            ->where('households.is_archived', 0)
            ->where('households.household_status_id', 6)
            ->select(
                'households.english_name as household',
                'communities.english_name as community_name',
                'old_communities.english_name as old_community_name',
                DB::raw('"Pending Reconnected - Displaced" as status'),

                DB::raw('NULL as meter_number'),
                DB::raw('NULL as meter_case_name_english'),
                DB::raw('NULL as meter_active'),
                DB::raw('NULL as installation_date'),
                DB::raw('NULL as daily_limit'),

                DB::raw('CASE WHEN households.number_of_male IS NULL
                    OR households.number_of_female IS NULL
                    OR households.number_of_adults IS NULL
                    OR households.number_of_children IS NULL
                    THEN "Missing Details" ELSE "Complete" END'),

                'households.number_of_male',
                'households.number_of_female',
                'households.number_of_adults',
                'households.number_of_children',

                DB::raw('"No Discrepancy" as discrepancies_status'),
                'households.phone_number',
                DB::raw('NULL as donors')
            );

        $confirmed = DB::table('displaced_households')
            ->join('households', 'households.id', 'displaced_households.household_id')
            ->join('communities as old_communities', 'displaced_households.old_community_id', 'old_communities.id')
            ->join('communities', 'households.community_id', 'communities.id')
            ->where('displaced_households.is_archived', 0)
            ->where('households.is_archived', 0)
            ->where('households.household_status_id', 11)
            ->select(
                'households.english_name as household',
                'communities.english_name as community_name',
                'old_communities.english_name as old_community_name',
                DB::raw('"Relocated - Confirmed" as status'),

                DB::raw('NULL as meter_number'),
                DB::raw('NULL as meter_case_name_english'),
                DB::raw('NULL as meter_active'),
                DB::raw('NULL as installation_date'),
                DB::raw('NULL as daily_limit'),

                DB::raw('CASE WHEN households.number_of_male IS NULL
                    OR households.number_of_female IS NULL
                    OR households.number_of_adults IS NULL
                    OR households.number_of_children IS NULL
                    THEN "Missing Details" ELSE "Complete" END'),

                'households.number_of_male',
                'households.number_of_female',
                'households.number_of_adults',
                'households.number_of_children',

                DB::raw('"No Discrepancy" as discrepancies_status'),
                'households.phone_number',
                DB::raw('NULL as donors')
            );

        $activeNoMeter = DB::table('displaced_households')
            ->join('all_energy_meters', 'all_energy_meters.household_id', '=', 'displaced_households.household_id')
            ->join('households', 'households.id', '=', 'displaced_households.household_id')
            ->join('communities as old_communities', 'displaced_households.old_community_id', 'old_communities.id')
            ->join('communities', 'households.community_id', 'communities.id')
            ->where('households.household_status_id', 14)
            ->where('all_energy_meters.is_archived', 0)
            ->where('households.is_archived', 0)
            ->select(
                'households.english_name as household',
                'communities.english_name as community_name',
                'old_communities.english_name as old_community_name',
                DB::raw('"Relocated - Served, No Meter" as status'),

                DB::raw('NULL as meter_number'),
                DB::raw('NULL as meter_case_name_english'),
                DB::raw('"No" as meter_active'),
                DB::raw('NULL as installation_date'),
                DB::raw('NULL as daily_limit'),

                DB::raw('"Complete"'),
                'households.number_of_male',
                'households.number_of_female',
                'households.number_of_adults',
                'households.number_of_children',

                DB::raw('"No Discrepancy"'),
                'households.phone_number',
                DB::raw('NULL as donors')
            );

        $served = DB::table('displaced_households')
            ->join('all_energy_meters', 'all_energy_meters.household_id', '=', 'displaced_households.household_id')
            ->join('households', 'households.id', '=', 'displaced_households.household_id')
            ->join('meter_cases', 'all_energy_meters.meter_case_id', 'meter_cases.id')
            ->join('communities as old_communities', 'displaced_households.old_community_id', 'old_communities.id')
            ->join('communities', 'households.community_id', 'communities.id')
            ->leftJoin('all_energy_meter_donors', 'all_energy_meters.id', 'all_energy_meter_donors.all_energy_meter_id')
            ->leftJoin('donors', 'all_energy_meter_donors.donor_id', 'donors.id')
            ->where('households.household_status_id', 4)
            ->where('displaced_households.is_archived', 0)
            ->where('all_energy_meters.is_archived', 0)
            ->where('households.is_archived', 0)
            ->where('all_energy_meters.meter_case_id', 1)
            ->where('all_energy_meters.energy_system_cycle_id', $cycleId)
            ->select(
                'households.english_name as household',
                'communities.english_name as community_name',
                'old_communities.english_name as old_community_name',
                DB::raw('"Relocated - Served" as status'),

                'all_energy_meters.meter_number',
                'meter_cases.meter_case_name_english',
                'all_energy_meters.meter_active',
                'all_energy_meters.installation_date',
                'all_energy_meters.daily_limit',

                DB::raw('"Complete"'),
                'households.number_of_male',
                'households.number_of_female',
                'households.number_of_adults',
                'households.number_of_children',

                DB::raw('"No Discrepancy"'),
                'households.phone_number',
                DB::raw('group_concat(DISTINCT donors.donor_name) as donors')
            )->groupBy('displaced_households.id'); 

            
        if ($cycleId) {

            $displacedToTown->where('households.energy_system_cycle_id', $cycleId);
            $displaced->where('households.energy_system_cycle_id', $cycleId);
            $confirmed->where('households.energy_system_cycle_id', $cycleId);
            $activeNoMeter->where('all_energy_meters.energy_system_cycle_id', $cycleId);
            $served->where('all_energy_meters.energy_system_cycle_id', $cycleId);
        }

        return $displacedToTown
            ->unionAll($displaced)
            ->unionAll($confirmed)
            ->unionAll($activeNoMeter)
            ->unionAll($served)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Household',
            'New Community',
            'Displaced Community',
            'Relocation Status',
            'Meter Number',
            'Meter Case',
            'Meter Active',
            'Installation Date',
            'Daily Limit',
            'All Details',
            'Number of Male',
            'Number of Female',
            'Number of Adults',
            'Number of Children',
            'Discrepancy',
            'Phone Number',
            'Donors',
        ];
    }

    public function title(): string
    {
        return 'Relocated Households (All)';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => fn ($event) =>
                $event->sheet->getDelegate()->freezePane('A2'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:Q1');

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
