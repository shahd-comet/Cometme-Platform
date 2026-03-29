<?php

namespace App\Exports;

use App\Models\AgricultureHolder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AgricultureHolderSmartboardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{
    /**
     * Collection of completed agriculture holders only
     *
     * @return \Illuminate\Support\Collection
     */
    /**
     * Keep the collection so AfterSheet can access model files.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $collection = AgricultureHolder::with(['household', 'community', 'agricultureSystems'])
            ->whereHas('agricultureHolderStatus', function($q) {
                $q->where('english_name', 'Completed');
            })
            ->orderBy('completed_date', 'desc')
            ->get();

        $this->rows = $collection;
        return $collection;
    }

    /**
     * Map each model to a row
     */
    public function map($item): array
    {
        $englishName = $item->household->english_name ?? '';
        $cometId = $item->household->comet_id ?? '';
        // leave QR cell blank — we'll insert the PNG into column C in AfterSheet
        $qrCode = '';
        $community = $item->community->english_name ?? '';
        $requestedDate = $item->requested_date ? $item->requested_date->format('Y-m-d') : '';
        $confirmationDate = $item->confirmation_date ? $item->confirmation_date->format('Y-m-d') : '';
        $installationDate = $item->completed_date ? $item->completed_date->format('Y-m-d') : '';
        $agriSystems = '';
        if ($item->agricultureSystems && $item->agricultureSystems->count() > 0) {
            $agriSystems = $item->agricultureSystems->pluck('name')->filter()->join(', ');
        }
        $installationType = '';
        if (!empty($item->agriculture_installation_types_id)) {
            // lazy lookup to avoid extra eager loads
            $type = \App\Models\AgricultureInstallationType::find($item->agriculture_installation_types_id);
            $installationType = $type ? ($type->english_name ?? $type->name ?? '') : '';
        }

        return [
            $englishName,
            $cometId,
            $qrCode,
            $community,
            $requestedDate,
            $confirmationDate,
            $installationDate,
            $agriSystems,
            $installationType,
        ];
    }

    public function headings(): array
    {
        return [
            'English Name',
            'Comet ID',
            'QR Code (comet_id)',
            'Community',
            'Requested Date',
            'Confirmation Date',
            'Installation Date',
            'Agriculture System',
            'Type of Installation',
        ];
    }

    public function title(): string
    {
        return 'Smart Board Data';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:I1');
                // Ensure column C is wide enough for the QR images
                $sheet->getColumnDimension('C')->setWidth(18);

                // Insert QR images into column C for each data row using stored PNGs when available
                if (!empty($this->rows) && $this->rows->count() > 0) {
                    $tmpFiles = [];
                    foreach ($this->rows as $index => $item) {
                        $row = $index + 2; // header is row 1

                        // Prefer stored qrcode_path (public disk)
                        $pngPath = null;
                        try {
                            if (!empty($item->qrcode_path) && Storage::disk('public')->exists($item->qrcode_path)) {
                                $ext = strtolower(pathinfo($item->qrcode_path, PATHINFO_EXTENSION));
                                // only accept raster images here
                                if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif'])) {
                                    $pngPath = storage_path('app/public/' . $item->qrcode_path);
                                } else {
                                    // if stored file is SVG, prefer fallback to generate/fetch PNG
                                    $pngPath = null;
                                }
                            }
                        } catch (\Exception $e) {
                            Log::warning('AgricultureHolderSmartboardExport: Storage check failed for holder id ' . ($item->id ?? 'unknown') . ' - ' . $e->getMessage());
                            $pngPath = null;
                        }

                        // Fallback: try to fetch PNG from Google Charts using comet_id
                        if (empty($pngPath)) {
                            $cometId = $item->household->comet_id ?? ($item->id ?? null);
                            if (empty($cometId)) {
                                continue;
                            }
                            $qrUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($cometId) . '&chld=L|1';
                            $imageContents = false;
                            if (ini_get('allow_url_fopen')) {
                                $imageContents = @file_get_contents($qrUrl);
                                if ($imageContents === false) {
                                    Log::warning('AgricultureHolderSmartboardExport: file_get_contents failed for URL ' . $qrUrl . ' row ' . $row);
                                }
                            }
                            if ($imageContents === false || $imageContents === null) {
                                $ch = curl_init($qrUrl);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_FAILONERROR, false); // we'll check HTTP code ourselves
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
                                $imageContents = curl_exec($ch);
                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                $contentLength = is_string($imageContents) ? strlen($imageContents) : 0;
                                if ($imageContents === false) {
                                    $err = curl_error($ch);
                                    Log::warning('AgricultureHolderSmartboardExport: curl_exec returned false for URL ' . $qrUrl . ' row ' . $row . ' error: ' . $err);
                                } else {
                                    if ($httpCode >= 400) {
                                        Log::warning('AgricultureHolderSmartboardExport: curl fetch HTTP ' . $httpCode . ' for URL ' . $qrUrl . ' row ' . $row . ' content_len=' . $contentLength);
                                    } else {
                                        Log::info('AgricultureHolderSmartboardExport: curl fetch OK for URL ' . $qrUrl . ' row ' . $row . ' http=' . $httpCode . ' len=' . $contentLength);
                                    }
                                }
                                curl_close($ch);
                            }

                            if ($imageContents === false || $imageContents === null) {
                                continue;
                            }

                            $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'qr_' . $row . '_' . uniqid() . '.png';
                            $written = @file_put_contents($tmpFile, $imageContents);
                            if ($written === false || !file_exists($tmpFile) || filesize($tmpFile) === 0) {
                                Log::warning('AgricultureHolderSmartboardExport: failed to write tmp QR file for row ' . $row . ' path=' . $tmpFile . ' written=' . var_export($written, true));
                                // ensure file does not remain as empty
                                if (file_exists($tmpFile)) {
                                    @unlink($tmpFile);
                                }
                                $pngPath = null;
                            } else {
                                $pngPath = $tmpFile;
                                $tmpFiles[] = $tmpFile;
                            }
                        }

                        if (!empty($pngPath) && file_exists($pngPath)) {
                            // clear any text in the cell so the image shows cleanly
                            try {
                                $sheet->setCellValue('C' . $row, '');
                            } catch (\Exception $e) {
                                // ignore
                            }
                            try {
                                $drawing = new Drawing();
                                $drawing->setName('QR');
                                $drawing->setDescription('QR Code (comet_id)');
                                $drawing->setPath($pngPath);
                                $drawing->setHeight(60);
                                $drawing->setCoordinates('C' . $row);
                                $drawing->setOffsetX(5);
                                $drawing->setWorksheet($sheet);
                                $sheet->getRowDimension($row)->setRowHeight(45);
                                Log::info('AgricultureHolderSmartboardExport: added drawing for row ' . $row . ' path=' . $pngPath);
                            } catch (\Exception $e) {
                                Log::warning('AgricultureHolderSmartboardExport: failed to add drawing for row ' . $row . ' - ' . $e->getMessage());
                                continue;
                            }
                        }
                    }

                    // cleanup any temporary fallback files
                    if (!empty($tmpFiles)) {
                        register_shutdown_function(function() use ($tmpFiles) {
                            foreach ($tmpFiles as $f) {
                                if (file_exists($f)) {
                                    @unlink($f);
                                }
                            }
                        });
                    }
                }
            }
        ];
    }
}
