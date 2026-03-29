<?php

namespace App\Exports;

use App\Models\Replacement;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReplacementsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Replacement::with(['cameraCommunity.community', 'camera', 'nvrCamera', 'cameraReplacementIncident']);

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
            $query->whereDate('date_of_replacement', $this->request->date);
        }

        // the new export form parameter `incident_type` and filter by it when set.
        $incidentId = $this->request->input('incident_type', $this->request->input('camera_replacement_incident_id'));
        if (!empty($incidentId)) {
            $query->where('camera_replacement_incident_id', $incidentId);
        }

        return $query->get()->map(function ($item) {
            $donors = $item->donors()->with('donor')->get();
            $donorNames = $donors->pluck('donor.donor_name')->implode(', ');
            
            return [
                $item->cameraCommunity->community->english_name ?? 'N/A',   // Community Name
                $item->date_of_replacement,
                $item->damaged_camera_count,
                $item->new_camera_count,
                $item->damaged_sd_card_count ?? '0',  // Number of Damaged SD
                $item->new_sd_card_count ?? '0',  // Number of New SD
                $item->camera->model ?? 'N/A',
                $item->nvrCamera->model ?? 'None',
                $item->number_of_nvr,
                $item->cameraReplacementIncident->english_name ?? 'N/A',
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
            '# Damaged',
            '# New',
            '# Damaged SD',
            '# New SD',
            'Camera Type',
            'NVR',
            '# NVRs',
            'Incident Type',
            'Donors',
            'Notes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->setAutoFilter('A1:L1');
        return [];
    }
}
