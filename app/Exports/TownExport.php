<?php

namespace App\Exports;

use App\Models\Town;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TownExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Return collection for export
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Town::with('region')
            ->where('is_archived', false)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($town) {
                return [
                    'english_name' => $town->english_name,
                    'arabic_name' => $town->arabic_name,
                    'region' => $town->region ? $town->region->english_name : null,
                    'comet_id' => $town->comet_id,
                ];
            });
    }

    /**
     * Headings for exported sheet
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'English Name',
            'Arabic Name',
            'Region',
            'Comet ID',
        ];
    }
}
