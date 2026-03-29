<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use DB; 

class CameraCommunityExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
    WithStyles, WithMultipleSheets
{
    protected $request;

    function __construct($request) {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Summary' => new SummarySheet($this->request),
            'Installed Cameras' => new InstalledCamerasSheet($this->request),
            'Camera Compounds' => new CameraCompoundsSheet($this->request),
            'Cameras Incidents' => new CamerasIncidentsSheet($this->request),
        ];
    }

    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Camera Community Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class SummarySheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $request;

    function __construct($request) {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('camera_communities')
            ->leftJoin('communities', 'camera_communities.community_id', 'communities.id')
            ->leftJoin('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
            ->leftJoin('camera_community_types', 'camera_communities.id', 'camera_community_types.camera_community_id')
            ->leftJoin('camera_community_additions', 'camera_communities.id', 'camera_community_additions.camera_community_id')
            ->leftJoin('camera_community_replacements', 'camera_communities.id', 'camera_community_replacements.camera_community_id')
            ->where('camera_communities.is_archived', 0)
            ->select([
                DB::raw('COUNT(DISTINCT camera_communities.id) as total_installations'),
                DB::raw('SUM(DISTINCT camera_community_types.number) as total_initial_cameras'),
                DB::raw('SUM(DISTINCT COALESCE(camera_community_additions.number_of_cameras, 0)) as total_added_cameras'),
                DB::raw('SUM(DISTINCT COALESCE(camera_community_replacements.new_camera_count, 0)) as total_replaced_cameras'),
                DB::raw('SUM(DISTINCT COALESCE(camera_community_replacements.damaged_camera_count, 0)) as total_damaged_cameras'),
                DB::raw('COUNT(DISTINCT communities.id) as total_communities'),
                DB::raw('COUNT(DISTINCT CASE WHEN camera_communities.compound_id IS NOT NULL THEN camera_communities.id END) as total_compounds'),
                DB::raw('SUM(DISTINCT communities.number_of_people) as total_beneficiaries'),
                DB::raw('SUM(DISTINCT communities.number_of_household) as total_households')
            ]);

        if($this->request->sub_region) {
            $query->where("sub_regions.id", $this->request->sub_region);
        }
        if($this->request->community) {
            $query->where("communities.id", $this->request->community);
        }
        if($this->request->date) {
            $query->where("camera_communities.date", ">=", $this->request->date);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Total Installations',
            'Total Initial Cameras',
            'Total Added Cameras',
            'Total Replaced Cameras',
            'Total Damaged Cameras',
            'Total Communities',
            'Total Compounds',
            'Total Beneficiaries',
            'Total Households'
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:I1');
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class InstalledCamerasSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $request;

    function __construct($request) {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('camera_communities')
            ->leftJoin('communities', 'camera_communities.community_id', 'communities.id')
            ->leftJoin('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
            ->leftJoin('repositories', 'camera_communities.repository_id', 'repositories.id') 
            ->leftJoin('regions as repository_regions', 'repositories.region_id', 'repository_regions.id') 
            ->leftJoin('sub_regions as repository_sub_regions', 'repositories.sub_region_id', 'repository_sub_regions.id')
            ->leftJoin('households', 'camera_communities.household_id', 'households.id')
            ->leftJoin('camera_community_types', 'camera_communities.id', 'camera_community_types.camera_community_id')
            ->leftJoin('cameras', 'camera_community_types.camera_id', 'cameras.id')
            ->leftJoin('nvr_community_types', 'camera_communities.id', 'nvr_community_types.camera_community_id')
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
                DB::raw('IFNULL(communities.english_name, CONCAT(repositories.name, " Warehouse")) as community'),
                DB::raw('IFNULL(regions.english_name, repository_regions.english_name) as region'),
                DB::raw('IFNULL(sub_regions.english_name, repository_sub_regions.english_name) as sub_region'),
                DB::raw('IFNULL(compounds.english_name, "") as compound_name'),
                DB::raw('CASE 
                    WHEN camera_communities.community_id IS NOT NULL THEN "Community"
                    WHEN camera_communities.repository_id IS NOT NULL THEN "Comet warehouse"
                    ELSE "Unknown"
                END as community_type'),
                'camera_communities.date as initial_cameras_installation_date',
                'households.english_name as responsible',
                DB::raw('(SUM(DISTINCT camera_community_types.number) + 
                         SUM(DISTINCT COALESCE(camera_community_additions.number_of_cameras, 0)) - 
                         SUM(DISTINCT COALESCE(camera_community_replacements.damaged_camera_count, 0)) + 
                         SUM(DISTINCT COALESCE(camera_community_replacements.new_camera_count, 0))) as current_installed_cameras'),
                DB::raw('COUNT(DISTINCT CASE WHEN incident_equipment.name LIKE "%destroyed%" OR incident_equipment.name LIKE "%stolen%" THEN all_camera_incidents.id END) as destroyed_stolen_incidents'),
                DB::raw('COUNT(DISTINCT CASE WHEN incident_equipment.name LIKE "%taken%" THEN all_camera_incidents.id END) as taken_by_residents'),
                DB::raw('SUM(DISTINCT COALESCE(camera_community_additions.number_of_cameras, 0)) as added_cameras'),
                DB::raw('CASE WHEN communities.community_status_id = 5 THEN "Displaced" ELSE "No" END as displaced'),
                DB::raw('group_concat(DISTINCT cameras.model) as camera_models'),
                DB::raw('group_concat(DISTINCT camera_community_types.number) as camera_numbers'),
                DB::raw('SUM(DISTINCT nvr_community_types.number) as nvr_count'),
                DB::raw('group_concat(DISTINCT nvr_cameras.model) as nvr_models'),
                DB::raw('group_concat(DISTINCT nvr_community_types.number) as nvr_numbers'),
                'communities.number_of_household',
                DB::raw('communities.number_of_people as beneficiaries'),
                DB::raw('CASE WHEN communities.number_of_people > 0 THEN ROUND(communities.number_of_people * 0.5) ELSE 0 END as female_beneficiaries'),
                DB::raw('CASE WHEN communities.number_of_people > 0 THEN ROUND(communities.number_of_people * 0.3) ELSE 0 END as children_beneficiaries'),
                DB::raw('group_concat(DISTINCT CASE WHEN camera_community_donors.is_archived = 0 THEN donors.donor_name END) as donors'),
                DB::raw('COUNT(DISTINCT all_camera_incidents.id) as camera_incidents'),
                'camera_communities.notes',
                DB::raw('CASE WHEN communities.internet_service = "Yes" THEN "Yes" ELSE "No" END as has_internet'),
                'communities.internet_service_beginning_year as internet_year'
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

    public function headings(): array
    {
        return [
            "Community", "Region", "Sub Region", "Compound", "Community / Comet warehouse", 
            "Initial Cameras Installation Date", "Responsible", "# of currently installed cameras", 
            "# of destroyed or stolen cameras (Incident)", "# of cameras taken (by residents)", 
            "# of added cameras", "Displaced?", "Camera Models", "Camera Number", "# of NVRs", 
            "NVR Models", "NVR Number", "# of Households", "# of Beneficiaries", "Female", 
            "Children", "Donors", "Cameras incidents", "Notes", "Has Internet?", "Internet Year"
        ];
    }

    public function title(): string
    {
        return 'Installed Cameras';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:Z1');
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class CameraCompoundsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $request;

    function __construct($request) {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('camera_communities')
            ->leftJoin('communities', 'camera_communities.community_id', 'communities.id')
            ->leftJoin('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
            ->leftJoin('compounds', 'camera_communities.compound_id', 'compounds.id')
            ->leftJoin('households', 'camera_communities.household_id', 'households.id')
            ->leftJoin('camera_community_types', 'camera_communities.id', 'camera_community_types.camera_community_id')
            ->leftJoin('cameras', 'camera_community_types.camera_id', 'cameras.id')
            ->leftJoin('nvr_community_types', 'camera_communities.id', 'nvr_community_types.camera_community_id')
            ->leftJoin('nvr_cameras', 'nvr_community_types.nvr_camera_id', 'nvr_cameras.id')
            ->leftJoin('camera_community_donors', 'camera_communities.id', 'camera_community_donors.camera_community_id')
            ->leftJoin('donors', 'camera_community_donors.donor_id', 'donors.id')
            ->leftJoin('camera_community_additions', 'camera_communities.id', 'camera_community_additions.camera_community_id')
            ->leftJoin('camera_community_replacements', 'camera_communities.id', 'camera_community_replacements.camera_community_id')
            ->where('camera_communities.is_archived', 0)
            ->whereNotNull('camera_communities.compound_id')
            ->select([
                DB::raw('IFNULL(compounds.english_name, "") as compound_name'),
                DB::raw('IFNULL(compounds.arabic_name, "") as compound_arabic_name'),
                DB::raw('IFNULL(communities.english_name, "") as community_name'),
                DB::raw('IFNULL(regions.english_name, "") as region'),
                DB::raw('IFNULL(sub_regions.english_name, "") as sub_region'),
                'camera_communities.date as initial_cameras_installation_date',
                'households.english_name as responsible',
                DB::raw('(SUM(DISTINCT camera_community_types.number) + 
                         SUM(DISTINCT COALESCE(camera_community_additions.number_of_cameras, 0)) - 
                         SUM(DISTINCT COALESCE(camera_community_replacements.damaged_camera_count, 0)) + 
                         SUM(DISTINCT COALESCE(camera_community_replacements.new_camera_count, 0))) as current_installed_cameras'),
                DB::raw('group_concat(DISTINCT cameras.model) as camera_models'),
                DB::raw('group_concat(DISTINCT camera_community_types.number) as camera_numbers'),
                DB::raw('SUM(DISTINCT nvr_community_types.number) as nvr_count'),
                DB::raw('group_concat(DISTINCT nvr_cameras.model) as nvr_models'),
                DB::raw('group_concat(DISTINCT nvr_community_types.number) as nvr_numbers'),
                DB::raw('compounds.number_of_household as compound_households'),
                DB::raw('compounds.number_of_people as compound_beneficiaries'),
                DB::raw('group_concat(DISTINCT CASE WHEN camera_community_donors.is_archived = 0 THEN donors.donor_name END) as donors'),
                'camera_communities.notes',
                DB::raw('CASE WHEN communities.internet_service = "Yes" THEN "Yes" ELSE "No" END as has_internet'),
                'communities.internet_service_beginning_year as internet_year'
            ])
            ->groupBy('camera_communities.id')
            ->orderBy('camera_communities.date', 'desc');

        if($this->request->sub_region) {
            $query->where("sub_regions.id", $this->request->sub_region);
        }
        if($this->request->community) {
            $query->where("communities.id", $this->request->community);
        }
        if($this->request->date) {
            $query->where("camera_communities.date", ">=", $this->request->date);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            "Compound Name", "Compound Arabic Name", "Community", "Region", "Sub Region", 
            "Initial Cameras Installation Date", "Responsible", "# of currently installed cameras", 
            "Camera Models", "Camera Number", "# of NVRs", "NVR Models", "NVR Number", 
            "# of Compound Households", "# of Compound Beneficiaries", "Donors", "Notes", 
            "Has Internet?", "Internet Year"
        ];
    }

    public function title(): string
    {
        return 'Camera Compounds';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:R1');
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class CamerasIncidentsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $request;

    function __construct($request) {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('all_camera_incidents')
            ->leftJoin('all_incidents', 'all_camera_incidents.all_incident_id', 'all_incidents.id')
            ->leftJoin('communities', 'all_camera_incidents.community_id', 'communities.id')
            ->leftJoin('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
            ->leftJoin('all_camera_incident_damaged_equipment', 'all_camera_incidents.id', 'all_camera_incident_damaged_equipment.all_camera_incident_id')
            ->leftJoin('incident_equipment', 'all_camera_incident_damaged_equipment.incident_equipment_id', 'incident_equipment.id')
            ->leftJoin('incidents', 'all_incidents.incident_id', 'incidents.id')
            ->select([
                'communities.english_name as community',
                'regions.english_name as region',
                'sub_regions.english_name as sub_region',
                'all_incidents.date as incident_date',
                'incidents.english_name as incident_type',
                'incident_equipment.name as damaged_equipment',
                'all_camera_incident_damaged_equipment.count as equipment_count',
                'all_camera_incident_damaged_equipment.cost as equipment_cost',
                DB::raw('(all_camera_incident_damaged_equipment.count * all_camera_incident_damaged_equipment.cost) as total_cost'),
                'all_incidents.notes as incident_notes'
            ])
            ->orderBy('all_incidents.date', 'desc');

        if($this->request->sub_region) {
            $query->where("sub_regions.id", $this->request->sub_region);
        }
        if($this->request->community) {
            $query->where("communities.id", $this->request->community);
        }
        if($this->request->date) {
            $query->where("all_incidents.date", ">=", $this->request->date);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            "Community", "Region", "Sub Region", "Incident Date", "Incident Type", 
            "Damaged Equipment", "Equipment Count", "Equipment Cost", "Total Cost", "Incident Notes"
        ];
    }

    public function title(): string
    {
        return 'Cameras Incidents';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:J1');
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}