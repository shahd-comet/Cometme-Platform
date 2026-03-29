<?php

namespace App\Exports\EnergyHolder;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class ReactivatedEnergyExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
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
        $query =  DB::table('deactivated_energy_holders') 
            ->join('users', 'deactivated_energy_holders.user_id', 'users.id')
            ->join('all_energy_meters', 'deactivated_energy_holders.all_energy_meter_id', 'all_energy_meters.id')
            ->leftJoin('households', 'all_energy_meters.household_id', 'households.id')
            ->leftJoin('public_structures', 'all_energy_meters.public_structure_id', 'public_structures.id')
            ->join('communities', 'all_energy_meters.community_id', 'communities.id')
            ->join('regions', 'communities.region_id', 'regions.id')
            ->join('energy_systems', 'all_energy_meters.energy_system_id', 'energy_systems.id')
            ->join('energy_system_types', 'all_energy_meters.energy_system_type_id', 'energy_system_types.id')
            ->where('deactivated_energy_holders.is_archived', 0)
            ->select(
                DB::raw('IFNULL(households.english_name, public_structures.english_name) 
                    as holder'), 
                'communities.english_name as community_name',
                'regions.english_name as region',
                'deactivated_energy_holders.meter_number', 
                'energy_systems.name as energy_system',
                'energy_system_types.name as energy_type',
                'all_energy_meters.daily_limit',
                'all_energy_meters.installation_date',
                'deactivated_energy_holders.visit_date',
                'users.name as user_name',
                'deactivated_energy_holders.is_paid',  
                'deactivated_energy_holders.paid_amount',  
                'deactivated_energy_holders.deactivated_after_war',
                'deactivated_energy_holders.system_status', 
                'deactivated_energy_holders.is_return', 
                'deactivated_energy_holders.reactivation_date', 
                'deactivated_energy_holders.notes', 
            );

        if($this->request->community_id) {

            $query->where("all_energy_meters.community_id", $this->request->community_id);
        }
        if($this->request->type) {

            $query->where("all_energy_meters.installation_type_id", $this->request->type);
        }
        if($this->request->date_from) {

            $query->where("deactivated_energy_holders.visit_date", ">=", $this->request->date_from);
        }
        if($this->request->date_to) {

            $query->where("deactivated_energy_holders.visit_date", "<=", $this->request->date_to);
        }

        return $query->get();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["Energy Holder", "Community", "Region", "Meter Number", "Energy System", "Energy System Type",   
            "Daily Limit",  "Installation Date", 
        
            "Visit Date", "Submitted by", "Is Paid?", "Paid Amount", "Deactivation after the war?", "System Status", 
            "Is Return?", "Reactivation Date", "Notes"];
    }

    public function title(): string
    {
        return 'Reactivated Holders';
    }

    /**
     * Styling
     * 
     * @return response()
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:Q1');

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}