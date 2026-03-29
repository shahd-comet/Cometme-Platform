<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\User;
use App\Models\Community;
use App\Models\AllEnergyMeter;
use App\Models\CommunityService;
use App\Models\InternetUser;
use App\Models\Household;
use App\Models\PublicStructure;
use Auth;
use DB;   
use Route; 

class InternetUserExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
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
        $data = DB::table('internet_users')
            ->leftJoin('communities', 'internet_users.community_id', 'communities.id')
            ->leftJoin('regions', 'communities.region_id', 'regions.id')
            ->leftJoin('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
            ->leftJoin('town_holders', 'internet_users.town_holder_id', 'town_holders.id')
            ->leftJoin('towns', 'town_holders.town_id', 'towns.id')
            ->leftJoin('households', 'internet_users.household_id', 'households.id')
            ->leftJoin('public_structures', 'internet_users.public_structure_id', 
                'public_structures.id')
            ->join('internet_statuses', 'internet_users.internet_status_id', 'internet_statuses.id')
            ->LeftJoin('internet_user_donors', 'internet_users.id', 
                'internet_user_donors.internet_user_id')
            ->LeftJoin('internet_cluster_communities', 'internet_cluster_communities.community_id', 
                'communities.id')
            ->LeftJoin('internet_clusters', 'internet_cluster_communities.internet_cluster_id', 
                'internet_clusters.id')
            ->LeftJoin('donors', 'internet_user_donors.donor_id', 'donors.id')
            ->where('internet_users.is_archived', 0)
            ->select(
                DB::raw('IFNULL(households.english_name, IFNULL(public_structures.english_name, town_holders.english_name))
                    as exported_value'),
                DB::raw('IFNULL(households.arabic_name, IFNULL(public_structures.arabic_name, town_holders.arabic_name))
                    as exported_value_arabic'),
                'communities.english_name as community_name',
                'towns.english_name as town_name',
                DB::raw("
                    CASE
                        WHEN town_holders.is_activist = 1 THEN 'Activist'
                        WHEN town_holders.is_community_internal = 1 THEN 'Community Internal'
                        WHEN communities.id IS NOT NULL THEN 'Community Holder'
                        WHEN towns.id IS NOT NULL THEN 'Town Holder'
                        ELSE 'Unknown'
                    END as type
                "),
                DB::raw('IFNULL(households.phone_number, IFNULL(public_structures.phone_number, town_holders.phone_number)) 
                    as phone_number'),
                'internet_clusters.name as cluster_name',
                'regions.english_name as region', 'sub_regions.english_name as sub_region',
                'internet_users.start_date', 
                DB::raw('CASE WHEN internet_users.paid = 1 THEN "Yes" ELSE "No" END as paid'),
                DB::raw('CASE 
                    WHEN internet_users.is_hotspot = 1 AND internet_users.is_ppp = 0 THEN "Hotspot" 
                    WHEN internet_users.is_hotspot = 0 AND internet_users.is_ppp = 1 THEN "Broadband" 
                    ELSE NULL END AS system_type'),
                DB::raw('CASE WHEN internet_users.active = 1 THEN "Yes" ELSE "No" END as active'),
                DB::raw('CASE WHEN internet_users.is_expire = 1 THEN "Yes" ELSE "No" END as expire'),
                'internet_users.number_of_contract', 'internet_users.last_purchase_date',
                DB::raw('CASE WHEN internet_users.expired_gt_than_30d = 1 THEN "Yes" 
                    ELSE "No" END as expired_gt_than_30d'),
                DB::raw('CASE WHEN internet_users.expired_gt_than_60d = 1 THEN "Yes" 
                    ELSE "No" END as expired_gt_than_60d'),
                DB::raw('group_concat(donors.donor_name) as donors') 
            )->groupBy('internet_users.id'); 

        if($this->request->community) {

            $data->where("communities.english_name", $this->request->community);
        } 
        if($this->request->donor) {

            $data->where("community_donors.donor_id", $this->request->donor);
        }
        if($this->request->start_date) {
            
            $data->where("internet_users.start_date", ">=", $this->request->start_date);
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
        return ["Internet Holder", "Arabic Name", "Community", "Town", "Contract Type", "Phone Number", 
            "Cluster Name", "Region", "Sub Region", "Start Date", "Is Paid", "System Type", "Is Active", 
            "Is Expire", "Number of Contracts", "Last Purchase Date", "Expired > 30", "Expired > 60", "Donors"];
    }

    public function title(): string
    {
        return 'Internet Users';
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