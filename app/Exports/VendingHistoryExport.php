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

class VendingHistoryExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize,
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
        $data = DB::table('vending_histories')
            ->join('vendor_services', 'vending_histories.vendor_service_id', 'vendor_services.id')
            ->join('vendors', 'vendor_services.vendor_id', 'vendors.id')
            ->leftJoin('service_types', 'vendor_services.service_type_id', 'service_types.id')
            ->leftJoin('users', 'vending_histories.user_id', 'users.id')
            ->leftJoin('vendor_regions', 'vendors.vendor_region_id', 'vendor_regions.id')
            ->leftJoin('communities', 'vendors.community_id', 'communities.id')
            ->leftJoin('towns', 'vendors.town_id', 'towns.id')
            ->where('vending_histories.is_archived', 0)
            ->select(
                'vendors.english_name as vendor',
                'vendors.phone_number',
                'vendors.status',
                'vending_histories.visit_date',
                'service_types.service_name',
                'vending_histories.collecting_date_from',
                'vending_histories.collecting_date_to',
                'vending_histories.total_amount_due',
                'vending_histories.amount_collected',
                'vending_histories.remaining_balance',
                'users.name',
                'vending_histories.notes',
            )->groupBy('vending_histories.id');


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

        return ["Vending Point", "Phone Number", "Status", "Visit Date", "Service", "Collecting Date From",
            "Collecting Date To", "Total Amount", "Collected Amount", "Remaining Amount", "Visit By", "Notes"];
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
        $sheet->setAutoFilter('A1:M1');

        return [

            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Visiting Follow-Up';
    }
}