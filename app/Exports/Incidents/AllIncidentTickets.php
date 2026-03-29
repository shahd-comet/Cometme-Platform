<?php

namespace App\Exports\Incidents;

use App\Models\EnergyUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class AllIncidentTickets implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
    WithStyles
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
        $data = DB::table('all_maintenance_tickets')
            ->join('service_types', 'all_maintenance_tickets.service_type_id', 'service_types.id')
            ->join('communities', 'all_maintenance_tickets.community_id', 'communities.id')
            ->join('regions', 'communities.region_id','regions.id')
            ->join('sub_regions', 'communities.sub_region_id','sub_regions.id')
            ->join('maintenance_types', 'all_maintenance_tickets.maintenance_type_id', 'maintenance_types.id')
            ->join('maintenance_statuses', 'all_maintenance_tickets.maintenance_status_id', 'maintenance_statuses.id')
            
            ->join('all_incidents', function ($join) {
                $join->on('all_maintenance_tickets.comet_id_from_uss', 'all_incidents.comet_id');
            })

            ->leftJoin('maintenance_status_reasons', 'all_maintenance_tickets.maintenance_status_reason_id', 
                'maintenance_status_reasons.id')
            ->leftJoin('users', 'all_maintenance_tickets.assigned_to', 'users.id')
            ->where('all_maintenance_tickets.is_archived', 0)
            ->leftJoin('households', 'all_maintenance_tickets.comet_id', 'households.comet_id')
            ->leftJoin('public_structures', 'all_maintenance_tickets.comet_id', 'public_structures.comet_id')

            ->leftJoin('energy_systems', 'all_maintenance_tickets.comet_id', 'energy_systems.comet_id')
            ->leftJoin(DB::raw('(
                SELECT em.energy_system_id,
                    COUNT(DISTINCT h.id) AS total_households,
                    SUM(h.number_of_adults + h.number_of_children) AS total_beneficiaries,
                    SUM(h.number_of_male) AS male,
                    SUM(h.number_of_female) AS female,
                    SUM(h.number_of_adults) AS adults,
                    SUM(h.number_of_children) AS children
                FROM all_energy_meters em
                JOIN households h ON em.household_id = h.id
                WHERE h.is_archived = 0
                GROUP BY em.energy_system_id
            ) AS energy_counts'), 'energy_counts.energy_system_id', '=', 'energy_systems.id')


            ->leftJoin('energy_generator_communities', 'all_maintenance_tickets.comet_id', 'energy_generator_communities.comet_id')
            ->leftJoin('energy_turbine_communities', 'all_maintenance_tickets.comet_id', 'energy_turbine_communities.comet_id')


            ->leftJoin('water_systems', 'all_maintenance_tickets.comet_id', 'water_systems.comet_id')
            ->leftJoin(DB::raw('(
                SELECT ws.water_system_id,
                    COUNT(DISTINCT h.id) AS total_households,
                    SUM(h.number_of_adults + h.number_of_children) AS total_beneficiaries,
                    SUM(h.number_of_male) AS male,
                    SUM(h.number_of_female) AS female,
                    SUM(h.number_of_adults) AS adults,
                    SUM(h.number_of_children) AS children
                FROM all_water_holders ws
                JOIN households h ON ws.household_id = h.id
                WHERE h.is_archived = 0
                GROUP BY ws.water_system_id
            ) AS water_counts'), 'water_counts.water_system_id', '=', 'water_systems.id')

            ->leftJoin('internet_systems', 'all_maintenance_tickets.comet_id', 'internet_systems.comet_id')
            ->leftJoin(DB::raw('(
                SELECT isc.internet_system_id,
                    COUNT(DISTINCT h.id) as total_households,
                    SUM(h.number_of_adults + h.number_of_children) as total_beneficiaries,
                    SUM(h.number_of_male) as male,
                    SUM(h.number_of_female) as female,
                    SUM(h.number_of_adults) as adults,
                    SUM(h.number_of_children) as children
                FROM internet_system_communities isc
                JOIN internet_users iu ON iu.community_id = isc.community_id
                JOIN households h ON iu.household_id = h.id
                WHERE h.is_archived = 0
                GROUP BY isc.internet_system_id
            ) as internet_counts'), 'internet_counts.internet_system_id', '=', 'internet_systems.id')

       
            ->leftJoin('all_maintenance_ticket_actions', 'all_maintenance_tickets.id', 'all_maintenance_ticket_actions.all_maintenance_ticket_id')
            ->leftJoin('energy_issues', 'energy_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
            ->leftJoin('energy_actions', 'energy_issues.energy_action_id', 'energy_actions.id')
            ->leftJoin('action_categories as energy_categories', 'energy_categories.id', 'energy_actions.action_category_id')
            ->leftJoin('water_issues', 'water_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
            ->leftJoin('water_actions', 'water_issues.water_action_id', 'water_actions.id')
            ->leftJoin('action_categories as water_categories', 'water_categories.id', 'water_actions.action_category_id')
            ->leftJoin('internet_issues', 'internet_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
            ->leftJoin('internet_actions', 'internet_issues.internet_action_id', 'internet_actions.id')
            ->leftJoin('action_categories as internet_categories', 'internet_categories.id', 'internet_actions.action_category_id')
            
            ->leftJoin('refrigerator_issues', 'refrigerator_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
            ->leftJoin('refrigerator_actions', 'refrigerator_issues.refrigerator_action_id', 'refrigerator_actions.id')
            ->leftJoin('action_categories as refrigerator_categories', 'refrigerator_categories.id', 'refrigerator_actions.action_category_id')

            ->leftJoin('workshop_communities', function ($join) {
                $join->on('all_maintenance_tickets.community_id', 'workshop_communities.community_id')
                ->where('workshop_communities.workshop_type_id', 5);
            })
 
            ->select([
                "all_maintenance_tickets.comet_id_from_uss as comet_id",
                DB::raw("CASE 
                            WHEN households.english_name IS NOT NULL THEN 'Household'
                            WHEN public_structures.english_name IS NOT NULL THEN 'Public Structure'
                            WHEN energy_systems.name IS NOT NULL THEN 'Energy System'
                            WHEN water_systems.name IS NOT NULL THEN 'Water System'
                            WHEN internet_systems.system_name IS NOT NULL THEN 'Internet System'
                            WHEN energy_generator_communities.name IS NOT NULL THEN 'Generator'
                            WHEN energy_turbine_communities.name IS NOT NULL THEN 'Turbine'
                            ELSE 'Unknown'
                        END as agent_type"),
                
                DB::raw("COALESCE(households.english_name, public_structures.english_name, 
                        energy_systems.name, energy_generator_communities.name, 
                        energy_turbine_communities.name, water_systems.name, internet_systems.system_name) as agent"),
                'workshop_communities.date as publish_date',
                'communities.english_name as community_name',
                'regions.english_name as region', 
                'sub_regions.english_name as sub_region',
                'service_types.service_name as department',

                'users.name as assigned_to',
                'maintenance_statuses.name as status',
                'maintenance_status_reasons.english_name as reason',
                'maintenance_types.type',
                DB::raw('CONCAT(
                    COALESCE(energy_categories.arabic_name, refrigerator_categories.arabic_name, water_categories.arabic_name, 
                    internet_categories.arabic_name), " - ", 
                    GROUP_CONCAT(DISTINCT COALESCE(energy_actions.arabic_name, refrigerator_actions.arabic_name, water_actions.arabic_name, 
                    internet_actions.arabic_name)), " - ", 
                    GROUP_CONCAT(DISTINCT COALESCE(energy_issues.arabic_name, refrigerator_issues.arabic_name, water_issues.arabic_name, 
                    internet_issues.arabic_name))
                    ) as category_action_issue'
                ),
                DB::raw('COALESCE(energy_categories.english_name, refrigerator_categories.english_name, 
                    water_categories.english_name, internet_categories.english_name) as category'),
            
                DB::raw('COALESCE(energy_actions.english_name, refrigerator_actions.english_name,
                    water_actions.english_name, internet_actions.english_name) as action'),
                
                DB::raw('COALESCE(energy_issues.english_name, refrigerator_issues.english_name, 
                    water_issues.english_name, internet_issues.english_name) as issue'),
                
                'all_maintenance_tickets.created_by',
                'all_maintenance_tickets.start_date as visit_date', 
                'all_maintenance_tickets.completed_date', 

                DB::raw('IFNULL(TIMESTAMPDIFF(HOUR, all_maintenance_tickets.support_created_at, 
                    all_maintenance_tickets.supported_updated_at), 0) as hours_difference'),

                DB::raw('CASE 
                    WHEN (TIMESTAMPDIFF(HOUR, all_maintenance_tickets.support_created_at, 
                    all_maintenance_tickets.supported_updated_at)) <= 24 THEN "Within a day"

                    WHEN (TIMESTAMPDIFF(HOUR, all_maintenance_tickets.support_created_at, 
                    all_maintenance_tickets.supported_updated_at)) <= 168 THEN "Within a week"

                    WHEN (TIMESTAMPDIFF(HOUR, all_maintenance_tickets.support_created_at, 
                    all_maintenance_tickets.supported_updated_at)) <= 720 THEN "Within a month"

                    WHEN (TIMESTAMPDIFF(HOUR, all_maintenance_tickets.support_created_at, 
                    all_maintenance_tickets.supported_updated_at)) <= 1440 THEN "Over a month"

                    WHEN (TIMESTAMPDIFF(HOUR, all_maintenance_tickets.support_created_at, 
                    all_maintenance_tickets.supported_updated_at)) <= 2160 THEN "Over 2 months"

                    ELSE "Over 3 months" 
                END as days_flag'),


               // Total Households
                DB::raw('
                    COUNT(DISTINCT CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.id END)
                    + IFNULL(energy_counts.total_households, 0)
                    + IFNULL(water_counts.total_households, 0)
                    + IFNULL(internet_counts.total_households, 0) AS total_households
                '),

                // Total Beneficiaries
                DB::raw('
                    SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_adults + households.number_of_children ELSE 0 END)
                    + IFNULL(energy_counts.total_beneficiaries, 0)
                    + IFNULL(water_counts.total_beneficiaries, 0)
                    + IFNULL(internet_counts.total_beneficiaries, 0) AS total_beneficiaries
                '),

                // Male
                DB::raw('
                    SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_male ELSE 0 END)
                    + IFNULL(energy_counts.male, 0)
                    + IFNULL(water_counts.male, 0)
                    + IFNULL(internet_counts.male, 0) AS male
                '),

                // Female
                DB::raw('
                    SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_female ELSE 0 END)
                    + IFNULL(energy_counts.female, 0)
                    + IFNULL(water_counts.female, 0)
                    + IFNULL(internet_counts.female, 0) AS female
                '),

                // Adults
                DB::raw('
                    SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_adults ELSE 0 END)
                    + IFNULL(energy_counts.adults, 0)
                    + IFNULL(water_counts.adults, 0)
                    + IFNULL(internet_counts.adults, 0) AS adults
                '),

                // Children
                DB::raw('
                    SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_children ELSE 0 END)
                    + IFNULL(energy_counts.children, 0)
                    + IFNULL(water_counts.children, 0)
                    + IFNULL(internet_counts.children, 0) AS children
                '),

      
                DB::raw('
                    CASE
                        WHEN
                            (
                                SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_male ELSE 0 END)
                                + IFNULL(energy_counts.male, 0)
                                + IFNULL(water_counts.male, 0)
                                + IFNULL(internet_counts.male, 0)
                            )
                            +
                            (
                                SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_female ELSE 0 END)
                                + IFNULL(energy_counts.female, 0)
                                + IFNULL(water_counts.female, 0)
                                + IFNULL(internet_counts.female, 0)
                            )
                            !=
                            (
                                SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_adults ELSE 0 END)
                                + IFNULL(energy_counts.adults, 0)
                                + IFNULL(water_counts.adults, 0)
                                + IFNULL(internet_counts.adults, 0)
                            )
                            +
                            (
                                SUM(CASE WHEN households.id IS NOT NULL AND households.is_archived = 0 THEN households.number_of_children ELSE 0 END)
                                + IFNULL(energy_counts.children, 0)
                                + IFNULL(water_counts.children, 0)
                                + IFNULL(internet_counts.children, 0)
                            )
                        THEN "Yes"
                        ELSE "No"
                    END as discrepancy_flag
                '),

                'all_maintenance_tickets.notes'
            ])->groupBy('all_maintenance_tickets.id');

        if($this->request->community_id) {

            $data->where("all_maintenance_tickets.community_id", $this->request->community_id);
        }
        if($this->request->service_id) {

            $data->where("all_maintenance_tickets.service_type_id", $this->request->service_id);
        }
        if($this->request->maintenance_status_id) {

            $data->where("maintenance_statuses.id", $this->request->maintenance_status_id);
        }
        if($this->request->maintenance_type_id) {

            $data->where("maintenance_types.id", $this->request->maintenance_type_id);
        }
        if($this->request->completed_date_from) {

            $data->where("all_maintenance_tickets.completed_date", ">=", $this->request->completed_date_from);
        }
        if($this->request->completed_date_to) {

            $data->where("all_maintenance_tickets.completed_date", "<=", $this->request->completed_date_to);
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
        return ["Ticket #", "Agent Type", "Agent", "Publish Date", "Community", "Region", "Sub Region", "Department",
            "Assigned to", "Status", "Reason", "Type", "Resoultion (Arabic)", "Category", "Action", "Issue", "Created by", 
            "Ticket created on", "Completed Date", "Hours (completed - created)", "Days Flag", "# of Households",
            "# of Beneficiaries", "Male", "Female", "Adults", "Children", "Is Discrepancy?", "Notes"];
    }

    public function title(): string
    {
        return 'Incident Tickets';
    }

    /**
     * Styling
     *
     * @return response()
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:AC1');

        // Get the highest row
        $highestRow = $sheet->getHighestRow();

        // Conditional formatting for discrepancy_flag (column W)
        for ($row = 2; $row <= $highestRow; $row++) {
            $cell = 'AB' . $row;
            $value = $sheet->getCell($cell)->getValue();

            if ($value === 'Yes') {
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB(Color::COLOR_RED);
            } elseif ($value === 'No') {
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB(Color::COLOR_GREEN);
            }
        }

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}