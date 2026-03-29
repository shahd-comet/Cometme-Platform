<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class InternetIssuesExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
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
        $data = DB::table('internet_issues')
            ->join('internet_actions', 'internet_issues.internet_action_id', 'internet_actions.id')
            ->join('action_categories', 'internet_actions.action_category_id', 'action_categories.id')
            ->where('internet_issues.is_archived', 0) 
            ->where('internet_actions.is_archived', 0)
            ->select(
                'internet_issues.english_name as english_name', 
                'internet_issues.arabic_name as arabic_name', 
                'internet_actions.english_name as action_english',
                'internet_actions.arabic_name as action_arabic',
                'action_categories.english_name as category',
                'internet_issues.notes'
            ); 

        if($this->request->action_name) {

            $data->where("internet_actions.id", $this->request->action_name);
        } 

        if($this->request->action_category) {

            $data->where("action_categories.id", $this->request->action_category);
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
        return ["Issue (English)", "Issue (Arabic)", "Action (English)", "Action (Arabic)", "Action Category", 
            "Notes"];
    }

    public function title(): string
    {
        return 'Internet Issues';
    }

    /**
     * Styling
     *
     * @return response()
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:F1');

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}