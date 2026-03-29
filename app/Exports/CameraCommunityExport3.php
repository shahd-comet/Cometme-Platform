<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB; 

class CameraCommunityExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
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
        $query = DB::table('camera_communities')
            ->leftJoin('communities', 'camera_communities.community_id', 'communities.id')
            ->leftJoin('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
            ->leftJoin('repositories', 'camera_communities.repository_id', 'repositories.id') 
            ->leftJoin('regions as repository_regions', 'repositories.region_id', 
                'repository_regions.id') 
            ->leftJoin('sub_regions as repository_sub_regions', 'repositories.sub_region_id', 
                'repository_sub_regions.id')
            ->leftJoin('households', 'camera_communities.household_id', 'households.id')
            ->leftJoin('camera_community_types', 'camera_communities.id', 
                'camera_community_types.camera_community_id')
            ->leftJoin('cameras', 'camera_community_types.camera_id', 'cameras.id')
            ->leftJoin('nvr_community_types', 'camera_communities.id', 
                'nvr_community_types.camera_community_id')
            ->leftJoin('nvr_cameras', 'nvr_community_types.nvr_camera_id', 'nvr_cameras.id')
            ->leftJoin('camera_community_donors', 'camera_communities.id', 'camera_community_donors.camera_community_id')
            ->leftJoin('donors', 'camera_community_donors.donor_id', 'donors.id')
            ->leftJoin('compounds', 'camera_communities.compound_id', 'compounds.id')
            ->leftJoin('camera_community_additions', 'camera_communities.id', 'camera_community_additions.camera_community_id')
            ->leftJoin('camera_community_replacements', 'camera_communities.id', 'camera_community_replacements.camera_community_id')
            ->leftJoin('all_camera_incidents', function($join) {
                $join->on('camera_communities.community_id', '=', 'all_camera_incidents.community_id')
                     ->orOn('camera_communities.repository_id', '=', 'all_camera_incidents.repository_id');
            })
            ->leftJoin('all_incidents', 'all_camera_incidents.all_incident_id', 'all_incidents.id')
            ->leftJoin('all_camera_incident_damaged_equipment', 'all_camera_incidents.id', 'all_camera_incident_damaged_equipment.all_camera_incident_id')
            ->leftJoin('incident_equipment', 'all_camera_incident_damaged_equipment.incident_equipment_id', 'incident_equipment.id')
            ->where('camera_communities.is_archived', 0)
            ->select([
                // Basic Community Info
                DB::raw('IFNULL(communities.english_name, CONCAT(repositories.name, " Warehouse")) as community'),
                DB::raw('IFNULL(regions.english_name, repository_regions.english_name) as region'),
                DB::raw('IFNULL(sub_regions.english_name, repository_sub_regions.english_name) as sub_region'),
                DB::raw('CASE 
                    WHEN camera_communities.community_id IS NOT NULL THEN "Community"
                    WHEN camera_communities.repository_id IS NOT NULL THEN "Comet warehouse"
                    ELSE "Unknown"
                END as community_type'),
                
                // Installation Date
                'camera_communities.date as initial_cameras_installation_date',
                
                // Responsible Person
                'households.english_name as responsible',
                
                // Current Cameras Calculation
                DB::raw('(SUM(DISTINCT camera_community_types.number) + 
                         SUM(DISTINCT COALESCE(camera_community_additions.number_of_cameras, 0)) - 
                         SUM(DISTINCT COALESCE(camera_community_replacements.damaged_camera_count, 0)) + 
                         SUM(DISTINCT COALESCE(camera_community_replacements.new_camera_count, 0))) as current_installed_cameras'),
                
                // Incident Calculations
                DB::raw('COUNT(DISTINCT CASE WHEN incident_equipment.name LIKE "%destroyed%" OR incident_equipment.name LIKE "%stolen%" THEN all_camera_incidents.id END) as destroyed_stolen_incidents'),
                DB::raw('COUNT(DISTINCT CASE WHEN incident_equipment.name LIKE "%taken%" THEN all_camera_incidents.id END) as taken_by_residents'),
                
                // Added Cameras
                DB::raw('SUM(DISTINCT COALESCE(camera_community_additions.number_of_cameras, 0)) as added_cameras'),
                
                // Displaced Status
                DB::raw('CASE WHEN communities.community_status_id = 5 THEN "Displaced" ELSE "No" END as displaced'),
                
                // Camera Details
                DB::raw('group_concat(DISTINCT cameras.model) as camera_models'),
                DB::raw('group_concat(DISTINCT camera_community_types.number) as camera_numbers'),
                
                // NVR Details
                DB::raw('SUM(DISTINCT nvr_community_types.number) as nvr_count'),
                DB::raw('group_concat(DISTINCT nvr_cameras.model) as nvr_models'),
                DB::raw('group_concat(DISTINCT nvr_community_types.number) as nvr_numbers'),
                
                // Household and Beneficiary Info
                'communities.number_of_household',
                DB::raw('communities.number_of_people as beneficiaries'),
                DB::raw('CASE WHEN communities.number_of_people > 0 THEN 
                    ROUND(communities.number_of_people * 0.5) ELSE 0 END as female_beneficiaries'),
                DB::raw('CASE WHEN communities.number_of_people > 0 THEN 
                    ROUND(communities.number_of_people * 0.3) ELSE 0 END as children_beneficiaries'),
                
                // Donors
                DB::raw('group_concat(DISTINCT CASE WHEN camera_community_donors.is_archived = 0 
                    THEN donors.donor_name END) as donors'),
                
                // Camera Incidents
                DB::raw('COUNT(DISTINCT all_camera_incidents.id) as camera_incidents'),
                
                // Notes
                'camera_communities.notes',
                
                // Internet Status
                DB::raw('CASE WHEN communities.internet_service = "Yes" THEN "Yes" ELSE "No" END as has_internet'),
                'communities.internet_service_beginning_year as internet_year',
                
                // Compound Info
                DB::raw('IFNULL(compounds.english_name, "") as compound_name')
            ])
            ->groupBy('camera_communities.id')
            ->orderBy('camera_communities.date', 'desc'); 

        if($this->request->sub_region) {

            $query->where("sub_regions.id", $this->request->sub_region)
                ->orWhere("repository_sub_regions.id", $this->request->sub_region);
        }
        if($this->request->community) {

            $query->where("communities.id", $this->request->community);
        } 
        if($this->request->date) {

            $query->where("camera_communities.date", ">=", $this->request->date);
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
        return [
            "Community", 
            "Region", 
            "Sub Region", 
            "Community / Comet warehouse", 
            "Initial Cameras Installation Date", 
            "Responsible", 
            "# of currently installed cameras", 
            "# of destroyed or stolen cameras (Incident)", 
            "# of cameras taken (by residents)", 
            "# of added cameras", 
            "Displaced?", 
            "Camera Models", 
            "Camera Number", 
            "# of NVRs", 
            "NVR Models", 
            "NVR Number", 
            "# of Households", 
            "# of Beneficiaries", 
            "Female", 
            "Children", 
            "Donors", 
            "Cameras incidents", 
            "Notes", 
            "Has Internet?", 
            "Internet Year",
            "Compound"
        ];
    }

    public function title(): string
    {
        return 'Enhanced Installed Cameras Report';
    }

    /**
     * Styling
     *
     * @return response()
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:Z1');

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}