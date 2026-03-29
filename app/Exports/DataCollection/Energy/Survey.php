<?php

namespace App\Exports\DataCollection\Energy;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use DB;
 
class Survey implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
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
        $fixedList = [
            
            [
                'type' => 'select_multiple co_trainer', 
                'name' => 'select_co_trainer',
                'label:English (en)' => 'Team',
                'label:Arabic (ar)' => 'الطاقم',
                'hint' => false,
                'choices' => 'co_trainer',
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'no',
                'relevant' => false,
            ],

            [
                'type' => 'select_one region', 
                'name' => 'select_region',
                'label:English (en)' => 'Choose region',
                'label:Arabic (ar)' => 'اختر المنطقة/المدينة',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'select_one sub_region', 
                'name' => 'select_sub_region',
                'label:English (en)' => 'Choose sub region',
                'label:Arabic (ar)' => 'اختر البلدة/القرية',
                'hint' => false,
                'choices' => 'sub_region',
                'choice_filter' => '${select_region} = region',
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'select_one community', 
                'name' => 'select_community',
                'label:English (en)' => 'Choose community',
                'label:Arabic (ar)' => 'اختر التجمع',
                'hint' => false,
                'choices' => false,
                'choice_filter' => '${select_sub_region} = sub_region',
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'select_one compound', 
                'name' => 'select_compound',
                'label:English (en)' => 'Select compound',
                'label:Arabic (ar)' => 'اختر المنطقة داخل التجمع',
                'hint' => false,
                'choices' => 'compound',
                'choice_filter' => '${select_community} = community',
                'calculation' => false,
                'required' => 'no',
                'relevant' => false,
            ],
            [
                'type' => 'select_one household', 
                'name' => 'select_household_name',
                'label:English (en)' => 'Select households',
                'label:Arabic (ar)' => 'اختر العائلة',
                'hint' => false,
                'choices' => 'household', 
                'choice_filter' => '${select_community} = community',
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'integer', 
                'name' => 'phone_number',
                'label:English (en)' => 'Enter phone number',
                'label:Arabic (ar)' => 'ادخل رقم الهاتف',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => 'pulldata("households", "phone_number", "name", ${select_household_name})',
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'text', 
                'name' => 'meter_number',
                'label:English (en)' => 'Meter number',
                'label:Arabic (ar)' => 'رقم الساعة',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => 'pulldata("households", "meter_number", "name", ${select_household_name})',
                'required' => 'no',
                'relevant' => false,
            ],
            
            [
                'type' => 'date', 
                'name' => 'workshop_date',
                'label:English (en)' => 'Visit date',
                'label:Arabic (ar)' => 'تاريخ الزيارة',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'select_one deactivate_meter', 
                'name' => 'select_deactivate_meter',
                'label:English (en)' => 'Deactivation after the war?',
                'label:Arabic (ar)' => 'هل تم الغاء الساعة بعد الحرب؟',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'select_one reactivation', 
                'name' => 'select_reactivation',
                'label:English (en)' => 'Interested in reactivation?',
                'label:Arabic (ar)' => 'هل المستخدم مهتم باعادة تفعيل الساعة؟',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'date', 
                'name' => 'reactivation_date',
                'label:English (en)' => 'Reactivation date',
                'label:Arabic (ar)' => 'تاريخ التفعيل',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'no',
                'relevant' => '${select_reactivation} = "Yes"',
            ],
            [
                'type' => 'select_one fine_paid', 
                'name' => 'select_fine_paid',
                'label:English (en)' => 'Fine Paid?',
                'label:Arabic (ar)' => 'هل تم دفع الغرامة؟',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'no',
                'relevant' => '${select_reactivation} = "Yes"',
            ],
            [
                'type' => 'integer', 
                'name' => 'amount',
                'label:English (en)' => 'Fine amount',
                'label:Arabic (ar)' => 'كم الغرامة؟',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'yes',
                'relevant' => '${select_fine_paid} = "Yes"',
            ],
            [
                'type' => 'select_one system_status', 
                'name' => 'select_system_status',
                'label:English (en)' => 'State of batteries/system?',
                'label:Arabic (ar)' => 'حالة البطاريات/النظام؟',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'yes',
                'relevant' => false,
            ],
            [
                'type' => 'text', 
                'name' => 'deactivation_notes',
                'label:English (en)' => 'Notes',
                'label:Arabic (ar)' => 'ملاحظات',
                'hint' => false,
                'choices' => false,
                'choice_filter' => false,
                'calculation' => false,
                'required' => 'no',
                'relevant' => false,
                'appearance' => 'long-text'
            ]          
        ];
        
        $fixedListCollection = collect($fixedList);

        return $fixedListCollection;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ['type', 'name', 'label:English (en)', 'label:Arabic (ar)', 'hint', 'choices', 'choice_filter', 'calculation', 
            'required', 'relevant', 'appearance'];
    }


    public function title(): string
    {
        return 'survey';
    }

    public function startCell(): string
    {
        return 'A1';
    } 


    /**
     * Styling
     *
     * @return response()
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:I1');

        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}