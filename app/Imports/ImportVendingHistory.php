<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorService;
use App\Models\ServiceType;
use App\Models\VendingHistory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportVendingHistory implements ToModel, WithHeadingRow
{
    /**
     * Parse date safely (Excel numeric OR string)
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
            }

            return new \DateTime($value);

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Get data from KOBO
        if (!empty($row["vending_point"])) {

            $vendor = Vendor::where('english_name', 'like', '%' . $row["vending_point"] . '%')->first();

            $visitDate = $this->parseDate($row['visit_date'] ?? null);

            $service = ServiceType::where('service_name', 'like', '%' . $row["agent"] . '%')->first();

            $cleanName = preg_replace('/\d/', '', $row["user"] ?? '');
            $visitBy = User::where('name', 'like', '%' . $cleanName . '%')->first();

            if ($vendor && $service) {

                $vendorService = VendorService::where("vendor_id", $vendor->id)
                    ->where("service_type_id", $service->id)
                    ->first();

                if (!$vendorService) {
                    $vendorService = new VendorService();
                    $vendorService->vendor_id = $vendor->id;
                    $vendorService->service_type_id = $service->id;
                    $vendorService->save();
                }

                $collectingDateFrom = $this->parseDate($row['from_date'] ?? null);
                $collectingDateTo   = $this->parseDate($row['to_date'] ?? null);

                $newVendingHistory = new VendingHistory();
                $newVendingHistory->vendor_id = $vendor->id;
                $newVendingHistory->vendor_service_id = $vendorService->id;

                if ($visitDate) {
                    $newVendingHistory->visit_date = $visitDate->format('Y-m-d');
                }

                if ($collectingDateFrom) {
                    $newVendingHistory->collecting_date_from = $collectingDateFrom->format('Y-m-d');
                }

                if ($collectingDateTo) {
                    $newVendingHistory->collecting_date_to = $collectingDateTo->format('Y-m-d');
                }

                $newVendingHistory->total_amount_due = $row["total_amount_due"] ?? null;
                $newVendingHistory->amount_collected = $row["amount_collected"] ?? null;
                $newVendingHistory->remaining_balance = $row["remaining_balance"] ?? null;

                // Prevent crash if user not found
                $newVendingHistory->user_id = $visitBy ? $visitBy->id : null;

                $newVendingHistory->notes = !empty($row["notes"])
                    ? trim(htmlspecialchars($row["notes"]))
                    : null;

                $newVendingHistory->save();
            }
        }

        return null;
    }
}