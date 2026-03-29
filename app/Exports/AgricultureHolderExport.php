<?php

namespace App\Exports;

use App\Models\AgricultureHolder;
use App\Models\AgricultureInstallationType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgricultureHolderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    /**
     * Return collection for export
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return AgricultureHolder::with([
            'community',
            'household',
            'agricultureHolderStatus',
            'agricultureSystemCycle',
            'agricultureSystems',
            'agricultureSharedHolders',
            'agricultureHolderDonors.donor'
        ])->get();
    }

    /**
     * Map a single model to the row array
     */
    public function map($item): array
    {
        // Community
        $community = $item->community ? ($item->community->english_name ?? $item->community->name ?? '') : '';

        // Household (use english_name if available)
        $household = $item->household ? ($item->household->english_name ?? $item->household->english_name ?? '') : '';

        // Household Status (Agriculture)
        $holderStatus = $item->agricultureHolderStatus ? ($item->agricultureHolderStatus->english_name ?? $item->agricultureHolderStatus->name ?? '') : '';

        // Main/Shared Energy Holder: if there are shared holder entries -> Shared, else Main
        $mainOrShared = $item->agricultureSharedHolders && $item->agricultureSharedHolders->count() > 0 ? 'Shared' : 'Main';

        // Dates
        $requestedDate = $item->requested_date ? $item->requested_date->format('Y-m-d') : '';
        $confirmationDate = $item->confirmation_date ? $item->confirmation_date->format('Y-m-d') : '';
        $installationDate = $item->completed_date ? $item->completed_date->format('Y-m-d') : '';

        // Type of Installation
        $installationType = '';
        if (!empty($item->agriculture_installation_types_id)) {
            $type = AgricultureInstallationType::find($item->agriculture_installation_types_id);
            $installationType = $type ? ($type->english_name ?? $type->name ?? '') : '';
        }

        // System Cycle
        $systemCycle = $item->agricultureSystemCycle ? ($item->agricultureSystemCycle->name ?? '') : '';

        // Agriculture Systems (many-to-many)
        $agriSystems = '';
        if ($item->agricultureSystems && $item->agricultureSystems->count() > 0) {
            $agriSystems = $item->agricultureSystems->pluck('name')->filter()->values()->all();
            $agriSystems = implode(', ', $agriSystems);
        }

        // Herds / units / animals
        $sizeOfHerds = $item->size_of_herds ?? '';
        $azollaUnits = $item->azolla_unit ?? '';
        $numCows = $item->size_of_cow ?? '';
        $numCamels = $item->size_of_camel ?? '';
        $numChickens = $item->size_of_chicken ?? '';

        // Areas, notes
        $areaOfInstallation = $item->area_of_installation ?? '';
        $area = $item->area ?? '';
        $alternativeArea = $item->alternative_area ?? '';
        $notes = $item->notes ?? '';

        // Donors (collect donor_name)
        $donors = '';
        if ($item->agricultureHolderDonors && $item->agricultureHolderDonors->count() > 0) {
            $donorsArr = $item->agricultureHolderDonors->map(function ($d) {
                return $d->donor ? ($d->donor->donor_name ?? '') : '';
            })->filter()->values()->all();

            $donors = implode(', ', $donorsArr);
        }

        return [
            $community,
            $household,
            $holderStatus,
            $mainOrShared,
            $requestedDate,
            $confirmationDate,
            $installationDate,
            $installationType,
            $systemCycle,
            $agriSystems,
            $sizeOfHerds,
            $azollaUnits,
            $numCows,
            $numCamels,
            $numChickens,
            $areaOfInstallation,
            $area,
            $alternativeArea,
            $notes,
            $donors,
        ];
    }

    /**
     * Headings in requested order
     */
    public function headings(): array
    {
        return [
            'Community',
            'Household',
            'Household Status (Agriculture)',
            'Main/Shared Energy Holder',
            'Request Date',
            'Confirmation Date',
            'Installation Date',
            'Type of Installation',
            'System Cycle',
            'Agriculture System',
            'Size of Herds',
            'Azolla Units',
            'Number of Cows',
            'Number of Camels',
            'Number of Chickens',
            'Which area will you build the system in?',
            'Area',
            'Alternative Area',
            'Notes',
            'Donors',
        ];
    }

    public function title(): string
    {
        return 'Agriculture Holders';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:T1');
            },
        ];
    }
}
