<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents; 
use Maatwebsite\Excel\Events\AfterSheet;
use DB;  

class WaterUserHolder implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize,
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
        $data = DB::table('all_water_holders') 
            ->join('communities', 'all_water_holders.community_id', 'communities.id')
            ->join('regions', 'communities.region_id', 'regions.id')
            ->join('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
            ->LeftJoin('grid_public_structures', 'all_water_holders.public_structure_id', 
                'grid_public_structures.public_structure_id')
            ->LeftJoin('h2o_public_structures', 'all_water_holders.public_structure_id', 
                'h2o_public_structures.public_structure_id')
            ->LeftJoin('h2o_shared_public_structures', 'all_water_holders.public_structure_id', 
                'h2o_shared_public_structures.public_structure_id')
            ->LeftJoin('public_structures', 'all_water_holders.public_structure_id', 
                'public_structures.id')
            ->LeftJoin('households', 'all_water_holders.household_id', 'households.id')
            ->LeftJoin('h2o_users', function ($join) {
                $join->on('h2o_users.household_id', '=', 'households.id')
                    ->where('h2o_users.is_archived', 0);
            })
            ->LeftJoin('h2o_shared_users', 'h2o_shared_users.household_id', 'households.id')

            ->LeftJoin('grid_users', function ($join) {
                $join->on('all_water_holders.household_id', '=', 'grid_users.household_id')
                    ->where('grid_users.is_archived', 0);
            })
            ->LeftJoin('grid_shared_users', 'grid_shared_users.household_id', 'households.id')

            ->LeftJoin('water_network_users', function ($join) {
                $join->on('all_water_holders.household_id', '=', 'water_network_users.household_id')
                    ->where('water_network_users.is_archived', 0);
            })
            ->LeftJoin('community_supply_tank_users', 'community_supply_tank_users.household_id', 
                'households.id')
            ->LeftJoin('h2o_statuses', 'h2o_users.h2o_status_id', 'h2o_statuses.id')
            ->LeftJoin('all_water_holder_donors', 'all_water_holders.id', 
                'all_water_holder_donors.all_water_holder_id')
            ->LeftJoin('donors', 'all_water_holder_donors.donor_id', 'donors.id')

            ->LeftJoin('water_systems', 'all_water_holders.water_system_id', 'water_systems.id')

            ->where('all_water_holders.is_archived', 0)
            ->select([ 
                DB::raw('IFNULL(households.english_name, public_structures.english_name) 
                    as exported_value'),
                'all_water_holders.is_main', 
                DB::raw("
                    CONCAT_WS(', ',
                        IF(
                            h2o_users.id IS NOT NULL 
                            OR h2o_shared_users.id IS NOT NULL 
                            OR h2o_public_structures.public_structure_id IS NOT NULL
                            OR h2o_shared_public_structures.public_structure_id IS NOT NULL,
                            'H2O', 
                            NULL
                        ),
                        IF(
                            grid_users.id IS NOT NULL 
                            OR grid_shared_users.id IS NOT NULL 
                            OR grid_public_structures.public_structure_id IS NOT NULL, 
                            'GRID INTEGRATION', 
                            NULL
                        ),
                        IF(water_network_users.id IS NOT NULL, 'NETWORK', NULL),
                        IF(community_supply_tank_users.id IS NOT NULL, 'COMMUNITY TANK', NULL)
                    ) as system_types
                "),
                'communities.english_name as community_name',
                'regions.english_name as region', 'sub_regions.english_name as sub_region',

                'h2o_users.number_of_h20', 'h2o_users.installation_year', 'h2o_statuses.status',  
                'h2o_users.h2o_installation_date', 'h2o_users.number_of_bsf', 
                'h2o_users.is_delivery as h2o_is_delivery', 'h2o_users.is_complete as h2o_is_complete',
                'h2o_users.is_paid as h2o_is_paid',

                'grid_users.grid_integration_large', 'grid_users.large_date', 'grid_users.grid_integration_small', 
                'grid_users.small_date', 'grid_users.is_delivery as grid_is_delivery', 'grid_users.is_complete as grid_is_complete',
                'grid_users.is_paid as grid_is_paid',

                'water_systems.year as network_year',
                'water_network_users.is_delivery as network_is_delivery', 
                'water_network_users.is_complete as network_is_complete',
                
                'households.number_of_male', 'households.number_of_female', 
                'households.number_of_adults', 'households.number_of_children', 
                'households.phone_number',
                DB::raw('group_concat(donors.donor_name) as donors'),
            ])
            ->groupBy('all_water_holders.id'); 

        if($this->request->water_system_id) {

            if( $this->request->water_system_id == 1) {

                $data->where("h2o_users.is_archived", 0)
                    ->orWhere("h2o_shared_users.is_archived", 0)
                    ->orWhere("h2o_public_structures.is_archived", 0)
                    ->orWhere("h2o_shared_public_structures.is_archived", 0);
            } else if( $this->request->water_system_id == 2) {
                
                $data->where("grid_users.is_archived", 0)
                    ->orWhere("grid_shared_users.is_archived", 0)
                    ->orWhere("grid_public_structures.is_archived", 0);
            } else if( $this->request->water_system_id == 4) {

                $data->where("water_network_users.is_archived", 0);
            } else if( $this->request->water_system_id == 3) {

                $data->where("community_supply_tank_users.is_archived", 0);
            }
        } 

        if($this->request->complete) {
            
            $data->where("grid_users.is_complete", $this->request->complete)
                ->orWhere("grid_public_structures.is_complete", $this->request->complete);
        }
         
        if($this->request->community) {
            $data->where("communities.english_name", $this->request->community);
        } 
        if($this->request->h2o_installation_date_from) {
            $data->where("all_water_holders.installation_date", ">=", $this->request->h2o_installation_date_from);
        }
        if($this->request->h2o_installation_date) {
            $data->where("all_water_holders.installation_date", "<=", $this->request->h2o_installation_date);
        }

        return $data->get();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */ 
    public function headings(): array
    {
        return [
            "Water Holder (Household / Public)", "Main Holder", "System Types", 
            "Community", "Region", "Sub Region", 
            
            "H2O systems", "Installation Year (H2O)", "H2O Status", "Installation date (H2O)", 
            "Number of BSF", "Delivery (H2O)", "Complete (H2O)", "Paid (H2O)", 


            "Grid Integration Large", "Installation date (Grid Large)", "Grid Integration Small", 
            "Installation date (Grid Small)", "Delivery (Grid)", "Complete (Grid)", "Paid (Grid)", 

            "Installation Year (Network)", "Delivery (Network)", "Complete (Network)",

            "Number of male", "Number of Female", "Number of adults", 
            "Number of children", "Phone number", "Donors"];
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
                $sheet = $event->sheet->getDelegate();

                // Freeze first row
                $sheet->freezePane('A2');

                // Auto filter
                $sheet->setAutoFilter('A1:AD1');

                // Get the highest row 
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:'.$highestColumn.$highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));


                // Loop through each row starting from row 2
                for ($row = 2; $row <= $highestRow; $row++) {

                    // H2O columns (blue)
                    for ($col = 'G'; $col <= 'N'; $col++) {
                        $sheet->getStyle($col.$row)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFB0C4DE'); // Light blue
                    }

                    // Grid columns (green)
                    for ($col = 'O'; $col <= 'U'; $col++) {
                        $sheet->getStyle($col.$row)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FF90EE90'); // Light green
                    }

                    // Network columns (yellow)
                    for ($col = 'V'; $col <= 'X'; $col++) {
                        $sheet->getStyle($col.$row)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFFF99'); // Light yellow
                    }
                }
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
        $sheet->setAutoFilter('A1:AD1');

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'All Water Holders';
    }
}