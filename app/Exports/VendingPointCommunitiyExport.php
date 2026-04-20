<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents; 
use Maatwebsite\Excel\Events\AfterSheet;
use DB;  

class VendingPointCommunitiyExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize,
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
        $data = DB::table('community_vendors')
            ->leftJoin('service_types', 'community_vendors.service_type_id', 'service_types.id')
            ->leftJoin('communities', 'community_vendors.community_id', 'communities.id')
            ->leftJoin('vendor_user_names', 'community_vendors.vendor_username_id', 'vendor_user_names.id')
            ->leftJoin('vendors', 'community_vendors.vendor_id', 'vendors.id')
            ->leftJoin('vendor_regions', 'vendors.vendor_region_id', 'vendor_regions.id')
            ->leftJoin('towns', 'vendors.town_id', 'towns.id')
            ->where('community_vendors.is_archived', 0)
            ->select(
                'communities.english_name as community',
                'service_types.service_name',
                'vendors.english_name as vendor',
                'vendors.status',
                'vendors.phone_number',
                'vendor_user_names.name as username',
                'community_vendors.nis'
            )->groupBy('community_vendors.id');


        if($this->request->region_id) {

            $data->where("vendor_regions.id", $this->request->region_id);
        } 
        if($this->request->community_id) {

            $data->where("communities.id", $this->request->community_id);
        } 
        if($this->request->town_id) {

            $data->where("towns.id", $this->request->town_id);
        } 
        if($this->request->service_id) {

            $data->where("service_types.id", $this->request->service_id);
        } 
        if($this->request->vendor_id) {

            $data->where("vendors.id", $this->request->vendor_id);
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

        return ["Community", "Service", "Vending Point", "Status", "Phone Number",
            "Usernames", "NIS"];
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
        $sheet->setAutoFilter('A1:G1');

        return [

            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Served Communities';
    }
}