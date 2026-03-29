<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class AgricultureIssuesExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
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
        $data = DB::table('agriculture_issues')
            ->join('agriculture_actions', 'agriculture_issues.agriculture_action_id', 'agriculture_actions.id')
            ->join('action_categories', 'agriculture_actions.action_category_id', 'action_categories.id')
            ->where('agriculture_issues.is_archived', 0) 
            ->where('agriculture_actions.is_archived', 0)
            ->select(
                'agriculture_issues.english_name as english_name', 
                'agriculture_issues.arabic_name as arabic_name', 
                'agriculture_actions.english_name as action_english',
                'agriculture_actions.arabic_name as action_arabic',
                'action_categories.english_name as category',
                'agriculture_issues.notes as issue_notes',
                'agriculture_actions.notes as action_notes'
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
        return ["Issue (English)", "Issue (Arabic)", "Action (English)", "Action (Arabic)", "Action Category", 
            "Notes (Issue)", "Notes (Action)"];
    }

    public function title(): string
    {
        return 'Agriculture Issues-Actions';
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