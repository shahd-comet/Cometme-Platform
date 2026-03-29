<?php

namespace App\Exports;

use App\Models\HouseholdStatus;
use App\Models\PublicStructureStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents; 
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use DB;

class FBSUpgradetoMGUsers implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
    WithStyles, WithEvents
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = DB::table('all_energy_meters')
            ->leftJoin('households', 'all_energy_meters.household_id', 'households.id')
            ->leftJoin('communities', 'all_energy_meters.community_id', 'communities.id')
            ->leftJoin('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('meter_cases', 'all_energy_meters.meter_case_id', 'meter_cases.id')
            ->leftJoin('energy_systems', 'all_energy_meters.energy_system_id', 'energy_systems.id')

            // shared holders join
            ->leftJoin('household_meters as hm', function($join) {
                $join->on('hm.energy_user_id', '=', 'all_energy_meters.id')
                     ->where('hm.is_archived', 0);
            })
            ->leftJoin('households as hs2', 'hm.household_id', 'hs2.id')

            // join household_meters to calculate totals
            ->leftJoin('household_meters as hm_count', function($join){
                $join->on('hm_count.energy_user_id', '=', 'all_energy_meters.id')
                     ->where('hm_count.is_archived', 0);
            })
            ->leftJoin('all_energy_meter_donors', 'all_energy_meters.id',
                'all_energy_meter_donors.all_energy_meter_id')
            ->leftJoin('donors', 'all_energy_meter_donors.donor_id', 'donors.id')

            
            ->leftJoin('all_energy_meter_new_donors', 'all_energy_meters.id',
                'all_energy_meter_new_donors.all_energy_meter_id')
            ->leftJoin('donors as new_donors', 'all_energy_meter_new_donors.donor_id', 'new_donors.id')

            ->where('all_energy_meters.is_archived', 0)
            ->where('all_energy_meters.installation_type_id', 7)

            ->select(
                'communities.english_name as community',
                'all_energy_meters.meter_number as meter_number',
                DB::raw('all_energy_meters.id as energy_user_id'),
                DB::raw('COALESCE(households.english_name, households.arabic_name, "") as household'),
                DB::raw('COALESCE(hs2.english_name, hs2.arabic_name, "") as shared_household'),
                DB::raw("CASE WHEN hm.fbs_upgrade_new = 1 THEN 'New Shared' 
                    WHEN hm.fbs_upgrade_new = 0 THEN 'Old Shared' 
                    WHEN hm.fbs_upgrade_new = 2 THEN 'Old Main' 
                    ELSE '' END as flag_shared_old_new"),
                DB::raw('COALESCE(energy_systems.name, "") as system_type'),

                // total families on SMG
                DB::raw('GREATEST(1, COUNT(DISTINCT all_energy_meters.household_id) + COUNT(DISTINCT hm_count.household_id)) as total_families_smg'),

                // new families on SMG
                DB::raw('COUNT(DISTINCT CASE WHEN hm_count.fbs_upgrade_new = 1 THEN hm_count.household_id END) as new_families_smg'),

                // previous families on FBS
                DB::raw('(GREATEST(1, COUNT(DISTINCT all_energy_meters.household_id) + COUNT(DISTINCT hm_count.household_id)) - COUNT(DISTINCT CASE WHEN hm_count.fbs_upgrade_new = 1 THEN hm_count.household_id END)) as previous_families_fbs'),

                DB::raw('group_concat(DISTINCT CASE WHEN all_energy_meter_donors.is_archived = 0 
                    THEN donors.donor_name END) as old_donors'),

                DB::raw('group_concat(DISTINCT CASE WHEN all_energy_meter_new_donors.is_archived = 0 
                THEN new_donors.donor_name END) as new_donors'),
            )
            ->groupBy('all_energy_meters.id', 'communities.english_name', 'all_energy_meters.meter_number', 'households.english_name', 'households.arabic_name', 'hs2.english_name', 'hs2.arabic_name', 'hm.fbs_upgrade_new', 'energy_systems.name')
            ->orderBy('communities.english_name')
            ->orderBy('households.english_name');

        if ($this->request && !empty($this->request->energy_cycle_id)) {
            $query->where('households.energy_system_cycle_id', $this->request->energy_cycle_id);
        }

        $rows = $query->get();

        // Collapse repeated meter rows for shared holders
        $lastEnergyUserId = null;
        foreach ($rows as $row) {
            if ($lastEnergyUserId !== null && $row->energy_user_id == $lastEnergyUserId) {
                $row->community = '';
                $row->meter_number = '';
                $row->household = '';
                $row->system_type = '';
                $row->total_families_smg = '';
                $row->new_families_smg = '';
                $row->previous_families_fbs = '';
                $row->old_donors = '';
                $row->new_donors = '';
            } else {
                $lastEnergyUserId = $row->energy_user_id;
            }
            unset($row->energy_user_id);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Community',
            'Meter Number',
            'Household',
            'Shared Households',
            'Flag for shared (old, new)',
            'system Name',
            '# of total families on SMG (with shared)',
            'New # of families on SMG',
            'Previous # of families on FBS',
            'Old Donors',
            'New Donors',
        ];
    }

    public function title(): string
    {
        return 'FBS Upgrade to MG/SMG';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $highestCol = $sheet->getHighestColumn();
                $sheet->setAutoFilter('A1:'.$highestCol.'1');

                $highestRow = $sheet->getHighestRow();
                $sharedRange = 'D2:E'.$highestRow;
                $sheet->getStyle($sharedRange)->getAlignment()->setWrapText(true)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                $sheet->getStyle('D2:D'.$highestRow)->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('BEF9F7');

                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(20);

                $groupIndex = 0;
                $applyFill = false;
                for ($r = 2; $r <= $highestRow; $r++) {
                    $communityVal = trim((string)$sheet->getCell('A'.$r)->getValue());
                    if ($communityVal !== '') {
                        $groupIndex++;
                        $applyFill = ($groupIndex % 2) === 1;
                    }
                    $rowRange = 'A'.$r.':K'.$r;
                    if ($applyFill) {
                        $sheet->getStyle($rowRange)->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('C8CFCC');
                    } else {
                        $sheet->getStyle($rowRange)->getFill()->setFillType(Fill::FILL_NONE);
                    }
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('ADD8E6');

        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
