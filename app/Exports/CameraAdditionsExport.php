<?php

namespace App\Exports;

use App\Models\CameraCommunityAddition;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CameraAdditionsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = CameraCommunityAddition::with(['cameraCommunity.community', 'camera', 'nvrCamera']);

        if ($this->request->filled('region_filter')) {
            $query->whereHas('cameraCommunity.community', function ($q) {
                $q->where('region_id', $this->request->region_filter);
            });
        }

        if ($this->request->filled('community_filter')) {
            $query->whereHas('cameraCommunity', function ($q) {
                $q->where('community_id', $this->request->community_filter);
            });
        }

        if ($this->request->filled('date')) {
            $query->whereDate('date_of_addition', $this->request->date);
        }

        return $query->get()->map(function ($item) {
            $donors = $item->donors()->with('donor')->get();
            $donorNames = $donors->pluck('donor.donor_name')->implode(', ');
            
            return [
                $item->cameraCommunity->community->english_name ?? 'N/A',  // Community Name
                $item->date_of_addition,
                $item->number_of_cameras,
                $item->sd_card_number ?? 'N/A',  // SD Card Number
                $item->camera->model ?? 'N/A',
                $item->nvrCamera->model ?? 'None',
                $item->number_of_nvr,
                $donorNames ?: 'N/A',  // Donors
                $item->notes,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Community',
            'Date',
            '# New Cameras',
            'SD Card Number',
            'Camera Type',
            'NVR',
            '# NVRs',
            'Donors',
            'Notes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->setAutoFilter('A1:I1');
        return [];
    }
}
