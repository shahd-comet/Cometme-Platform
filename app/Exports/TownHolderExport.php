<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class TownHolderExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
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
        $data = DB::table('town_holders')
            ->join('towns', 'town_holders.town_id', 'towns.id')
            ->where('town_holders.is_archived', 0) 
            ->select(
                'town_holders.english_name as english_name', 
                'town_holders.arabic_name as arabic_name', 
                'town_holders.phone_number', 
                'towns.english_name as town_english',
                'towns.arabic_name as town_arabic',
                DB::raw("CASE WHEN town_holders.has_internet = 1 THEN 'Yes' ELSE 'No' END as has_internet"),
                DB::raw("CASE WHEN town_holders.has_refrigerator = 1 THEN 'Yes' ELSE 'No' END as has_refrigerator")
            ); 

        return $data->get();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["Holder (English)", "Holder (Arabic)", "Phone Number", "Town (English)", "Town (Arabic)", "Has Internet", 
            "Has Refrigerator"];
    }

    public function title(): string
    {
        return 'Town Holders';
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
}