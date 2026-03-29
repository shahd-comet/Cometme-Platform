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
use App\Models\CommunityService;
use App\Models\InternetUser;
use App\Models\Household;
use App\Models\PublicStructure;
use Auth;
use DB;  
use Route; 

class InternetIssue implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, 
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
        $queryHolders = DB::table('internet_users')
            ->join('communities', 'internet_users.community_id', 'communities.id')
            ->leftJoin('public_structures', 'internet_users.public_structure_id', 'public_structures.id')
            ->leftJoin('households', 'internet_users.household_id', 'households.id')
            ->where('internet_users.is_archived', 0)
            ->join('all_energy_meters', 'all_energy_meters.community_id', 'internet_users.community_id')
            ->select(
                'internet_users.id as id',
                DB::raw('IFNULL(households.english_name, public_structures.english_name) as contract_holder'),
                DB::raw('IFNULL(households.arabic_name, public_structures.arabic_name) as contract_holder_arabic'),
                'communities.english_name as community_name',
                DB::raw('IFNULL(households.phone_number, public_structures.phone_number) as contract_phone_number')
            )
            ->distinct()
            ->get();

        foreach($queryHolders as $q) {

            $internetUser = InternetUser::findOrFail($q->id);
            $meterNumbers = [];
            $meterCases = [];

            if($internetUser->household_id) {
                $allEnergyMeters = DB::table('all_energy_meters')
                    ->where('all_energy_meters.is_archived', 0)
                    ->where('all_energy_meters.household_id', $internetUser->household_id)
                    ->join('meter_cases', 'all_energy_meters.meter_case_id', 'meter_cases.id')
                    ->select(
                        'all_energy_meters.meter_number',
                        'meter_cases.meter_case_name_english'
                    )
                    ->get();
        
                foreach ($allEnergyMeters as $meter) {

                    $meterNumbers[] = $meter->meter_number;
                    $meterCases[] = $meter->meter_case_name_english;
                }
            } 
        
            if($internetUser->public_structure_id) {

                $allEnergyMeters = DB::table('all_energy_meters')
                    ->where('all_energy_meters.is_archived', 0)
                    ->where('all_energy_meters.public_structure_id', $internetUser->public_structure_id)
                    ->join('meter_cases', 'all_energy_meters.meter_case_id', 'meter_cases.id')
                    ->select(
                        'all_energy_meters.meter_number',
                        'meter_cases.meter_case_name_english'
                    )
                    ->get();
        
                foreach ($allEnergyMeters as $meter) {

                    $meterNumbers[] = $meter->meter_number;
                    $meterCases[] = $meter->meter_case_name_english;
                }
            }
        
            // Assign meter numbers to the query result
            $q->meter_numbers = $meterNumbers;
            $q->meter_cases = $meterCases;
        }
        
        return $queryHolders;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["Internet Holder ID", "Contract Holder", "Contract Holder Arabic", "Community", "Phone Number"];
    }

    public function title(): string
    {
        return 'Internet Issue';
    }

    /**
     * Styling
     *
     * @return response()
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:P1');

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}