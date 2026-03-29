<?php

namespace App\Imports;

use App\Models\MeterHistories;
use App\Models\AllEnergyMeter;
use App\Models\Community;
use App\Models\Household;
use App\Models\PublicStructure;
use App\Models\MeterHistoryReason;
use App\Models\MeterHistoryStatuses;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Exception;

class ImportMeterHistory implements ToModel, WithHeadingRow
{
    // Static counters to track import results
    public static $inserted = 0;
    public static $skipped = 0;

    public static function resetCounters(): void
    {
        self::$inserted = 0;
        self::$skipped = 0;
    }

    public static function stats(): array
    {
        return [
            'inserted' => self::$inserted,
            'skipped' => self::$skipped
        ];
    }

    public function model(array $row)
    {
        static $rowCount = 0;
        $rowCount++;
        
        // Log available Excel columns for debugging (only first row)
        if ($rowCount == 1) {
            \Log::info('Excel columns: ' . implode(', ', array_keys($row)));
        }
        
        \Log::info("Processing row #{$rowCount}");

        // Extract Excel columns based on your exact headers
        $mainEnergyMeter = trim($row['main_energy_meter'] ?? '');
        $meterStatus = trim($row['meter_status'] ?? '');
        $reason = trim($row['reason'] ?? '');
        $houseHolder = trim($row['householder'] ?? '');
        $community = trim($row['community'] ?? '');
        $oldCommunity = trim($row['old_community'] ?? $row['community'] ?? ''); // Try old_community first, fallback to community
        $householdStatus = trim($row['household_status'] ?? '');
        $changeDate = $row['change_date'] ?? null;
        $newMeterNumber = trim($row['new_meter_number_if_replaced'] ?? $row['new_meter_number'] ?? ''); // Try new_meter_number_if_replaced first
        $sharedUser = trim($row['shared_user'] ?? '');
        $newHousehold = trim($row['new_householdif_used_by_other'] ?? $row['new_household_if_used_by_other'] ?? $row['new_householdif_become_a_shared'] ?? $row['new_household'] ?? ''); // Try the actual Excel column name first
        $newCommunity = trim($row['new_community'] ?? '');
        $newHolderStatus = trim($row['new_holder_status'] ?? '');
        $oldMeterNumber = trim($row['old_meter_number'] ?? '');
        $notes = trim($row['notes'] ?? '');
        $cometId = trim($row['comet_id'] ?? $row['comet_ID'] ?? '');

        // Debug: Log HouseHolder value extraction
        if ($rowCount <= 5) { // Only log first 5 rows to avoid spam
            \Log::info("Row #{$rowCount}: HouseHolder Excel value = '{$houseHolder}'");
        }

        // Log extracted data for debugging
        \Log::info("Row #{$rowCount} data: main_energy_meter='{$mainEnergyMeter}', old_meter_number='{$oldMeterNumber}', new_meter_number='{$newMeterNumber}', householder='{$houseHolder}', new_household='{$newHousehold}', old_community='{$oldCommunity}', comet_id='{$cometId}'");
        
        // Special debugging for problematic records
        if (in_array($mainEnergyMeter, ['37187801818', '37232652398'])) {
            \Log::info("Row #{$rowCount} PROBLEMATIC RECORD: All Excel columns: " . json_encode($row));
            \Log::info("Row #{$rowCount} EXTRACTION TEST: new_householdif_used_by_other = '" . ($row['new_householdif_used_by_other'] ?? 'NOT FOUND') . "'");
            \Log::info("Row #{$rowCount} EXTRACTION RESULT: newHousehold = '{$newHousehold}'");
        }
        
        // Only skip if the entire row is completely empty
        if (empty($mainEnergyMeter) && empty($oldMeterNumber) && empty($houseHolder) && empty($notes)) {
            \Log::info("Row #{$rowCount}: Skipping completely empty row");
            self::$skipped++;
            return null;
        }

        // old_meter_number is completely different from main_energy_meter
        // If empty, we'll handle it in the database field mapping

        $data = [];

        // Find meter status first to determine lookup strategy
        // Resolve meter status text to id; create status when necessary and fallback to 'Unknown'
        $statusRecord = null;
        $data['meter_history_status_id'] = null;
        if (!empty($meterStatus)) {
            $statusRecord = MeterHistoryStatuses::where('english_name', $meterStatus)
                ->orWhere('arabic_name', $meterStatus)
                ->first();
            if ($statusRecord) {
                $data['meter_history_status_id'] = $statusRecord->id;
            } else {
                try {
                    $createdStatus = MeterHistoryStatuses::create([
                        'english_name' => $meterStatus,
                        'arabic_name' => null
                    ]);
                    $data['meter_history_status_id'] = $createdStatus->id;
                    \Log::warning("Row #{$rowCount}: Created MeterHistoryStatuses '{$meterStatus}' with ID {$createdStatus->id} from Excel text");
                } catch (\Exception $e) {
                    \Log::error("Row #{$rowCount}: Failed to create MeterHistoryStatuses for '{$meterStatus}': " . $e->getMessage());
                }
            }
        }

        // Ensure non-null status id to satisfy FK
        if (is_null($data['meter_history_status_id'])) {
            $fallbackStatus = MeterHistoryStatuses::firstOrCreate(
                ['english_name' => 'Unknown'],
                ['arabic_name' => null]
            );
            $data['meter_history_status_id'] = $fallbackStatus->id;
            \Log::warning("Row #{$rowCount}: meter_history_status_id missing — using fallback MeterHistoryStatuses ID {$fallbackStatus->id}");
        }

        // Determine which meter number to use for energy meter lookup
        $meterNumberForLookup = $mainEnergyMeter;
        $lookupSource = 'main_energy_meter';
        
        // For "Used by other" status, use old_meter_number instead of main_energy_meter
        if ($statusRecord && stripos($statusRecord->english_name, 'used by other') !== false) {
            if (!empty($oldMeterNumber)) {
                $meterNumberForLookup = $oldMeterNumber;
                $lookupSource = 'old_meter_number';
                \Log::info("Row #{$rowCount}: Using old_meter_number '{$oldMeterNumber}' for energy meter lookup (Used by other status)");
            } else {
                \Log::warning("Row #{$rowCount}: 'Used by other' status but old_meter_number is empty, falling back to main_energy_meter");
            }
        }

        // Find energy meter for lookup (used for household/community lookup)
        $energyMeterForLookup = null;
        if (!empty($meterNumberForLookup)) {
            $energyMeterForLookup = AllEnergyMeter::where('meter_number', $meterNumberForLookup)->first();
            if ($energyMeterForLookup) {
                \Log::info("Found energy meter ID: {$energyMeterForLookup->id} for meter: {$meterNumberForLookup} (from {$lookupSource})");
            } else {
                \Log::warning("Row #{$rowCount}: Energy meter not found in database: '{$meterNumberForLookup}' (from {$lookupSource})");
            }
        }

        // ALWAYS assign both all_energy_meter_id and main_energy_meter_id based on main_energy_meter
        if (!empty($mainEnergyMeter)) {
            $mainEnergyMeterRecord = AllEnergyMeter::where('meter_number', $mainEnergyMeter)->first();
            if ($mainEnergyMeterRecord) {
                $data['all_energy_meter_id'] = $mainEnergyMeterRecord->id;
                $data['main_energy_meter_id'] = $mainEnergyMeterRecord->id; // Always same ID
                \Log::info("Row #{$rowCount}: Assigned both all_energy_meter_id and main_energy_meter_id: {$mainEnergyMeterRecord->id} for main meter: {$mainEnergyMeter}");
            } else {
                \Log::warning("Row #{$rowCount}: Main energy meter not found in database: '{$mainEnergyMeter}' - creating fallback AllEnergyMeter record");
                try {
                    $fallbackMeter = new AllEnergyMeter();
                    $fallbackMeter->meter_number = $mainEnergyMeter;
                    if (!empty($data['household_id'])) $fallbackMeter->household_id = $data['household_id'];
                    if (!empty($data['publicstructure_id'])) $fallbackMeter->public_structure_id = $data['publicstructure_id'];
                    if (!empty($data['community_id'])) $fallbackMeter->community_id = $data['community_id'];
                    $fallbackMeter->is_archived = 0;
                    $fallbackMeter->is_main = 'No';
                    $fallbackMeter->meter_case_id = $fallbackMeter->meter_case_id ?? 1;
                    $fallbackMeter->installation_type_id = $fallbackMeter->installation_type_id ?? 4;
                    $fallbackMeter->save();
                    $data['all_energy_meter_id'] = $fallbackMeter->id;
                    $data['main_energy_meter_id'] = $fallbackMeter->id;
                    \Log::warning("Row #{$rowCount}: Created fallback AllEnergyMeter ID {$fallbackMeter->id} for meter '{$mainEnergyMeter}'");
                } catch (\Exception $e) {
                    \Log::error("Row #{$rowCount}: Failed to create fallback AllEnergyMeter: " . $e->getMessage());
                }
            }
        } else {
            // If mainEnergyMeter is empty, try using old_meter_number to find or create a meter
            if (!empty($oldMeterNumber)) {
                $mainEnergyMeterRecord = AllEnergyMeter::where('meter_number', $oldMeterNumber)->first();
                if ($mainEnergyMeterRecord) {
                    $data['all_energy_meter_id'] = $mainEnergyMeterRecord->id;
                    $data['main_energy_meter_id'] = $mainEnergyMeterRecord->id;
                    \Log::info("Row #{$rowCount}: Assigned meter IDs from old_meter_number: {$mainEnergyMeterRecord->id}");
                } else {
                    try {
                        $fallbackMeter = new AllEnergyMeter();
                        $fallbackMeter->meter_number = $oldMeterNumber;
                        if (!empty($data['household_id'])) $fallbackMeter->household_id = $data['household_id'];
                        if (!empty($data['publicstructure_id'])) $fallbackMeter->public_structure_id = $data['publicstructure_id'];
                        if (!empty($data['community_id'])) $fallbackMeter->community_id = $data['community_id'];
                        $fallbackMeter->is_archived = 0;
                        $fallbackMeter->is_main = 'No';
                        $fallbackMeter->meter_case_id = $fallbackMeter->meter_case_id ?? 1;
                        $fallbackMeter->installation_type_id = $fallbackMeter->installation_type_id ?? 4;
                        $fallbackMeter->save();
                        $data['all_energy_meter_id'] = $fallbackMeter->id;
                        $data['main_energy_meter_id'] = $fallbackMeter->id;
                        \Log::warning("Row #{$rowCount}: Created fallback AllEnergyMeter ID {$fallbackMeter->id} for old meter '{$oldMeterNumber}'");
                    } catch (\Exception $e) {
                        \Log::error("Row #{$rowCount}: Failed to create fallback AllEnergyMeter for old_meter_number: " . $e->getMessage());
                    }
                }
            } else {
                \Log::warning("Row #{$rowCount}: Main energy meter is empty - cannot assign meter IDs");
            }
        }

        // Store meter status (already found above for lookup strategy)
        if ($statusRecord) {
            $data['meter_history_status_id'] = $statusRecord->id;
        }
        
        // Store the status name directly from Excel
        if (!empty($meterStatus)) {
            $data['status_english_name'] = $meterStatus;
        }
        
        // // Store the meter number directly from Excel (main energy meter)
        // if (!empty($mainEnergyMeter)) {
        //     $data['meter'] = $mainEnergyMeter;
        // }
        
        // // Store comet_id directly from Excel
        // if (!empty($cometId)) {
        //     $data['comet_id'] = $cometId;
        //     \Log::info("Row #{$rowCount}: Setting comet_id to: '{$cometId}'");
        // }

        // Resolve meter history reason text -> id. Create a reason record when text provided but not found.
        $data['meter_history_reason_id'] = null;
        if (!empty($reason)) {
            $reasonRecord = MeterHistoryReason::where('english_name', $reason)
                ->orWhere('arabic_name', $reason)
                ->first();
            if ($reasonRecord) {
                $data['meter_history_reason_id'] = $reasonRecord->id;
            } else {
                // Create a new reason record using the text from Excel so relationships work
                try {
                    $created = MeterHistoryReason::create([
                        'english_name' => $reason,
                        'arabic_name' => null,
                        'meter_history_status_id' => $data['meter_history_status_id'] ?? null
                    ]);
                    $data['meter_history_reason_id'] = $created->id;
                    \Log::warning("Row #{$rowCount}: Created MeterHistoryReason '{$reason}' with ID {$created->id} from Excel text");
                } catch (\Exception $e) {
                    \Log::error("Row #{$rowCount}: Failed to create MeterHistoryReason for '{$reason}': " . $e->getMessage());
                }
            }
        }

        // Ensure a non-null reason id exists - use or create 'Unknown' fallback
        if (is_null($data['meter_history_reason_id'])) {
            $fallback = MeterHistoryReason::firstOrCreate(
                ['english_name' => 'Unknown'],
                ['arabic_name' => null, 'meter_history_status_id' => ($data['meter_history_status_id'] ?? null)]
            );
            $data['meter_history_reason_id'] = $fallback->id;
            \Log::warning("Row #{$rowCount}: meter_history_reason_id missing — using fallback MeterHistoryReason ID {$fallback->id}");
        }

        // Find householder - first try household, then public structure if not found
        if (!empty($houseHolder)) {
            // Clean up the householder name (remove extra spaces and fix encoding)
            $cleanedHouseHolder = preg_replace('/\s+/', ' ', trim($houseHolder));
            
            // Handle specific name mapping issues
            $nameMap = [
                'Yaser Mohammad Hamadeen' => ['Yaseer Mohammad Hamadeen', 'Yasser Mohammad Hamadeen'],
                'علي عمر علي ابو هنية' => ['Ali Omar Ali Abu Huniya', 'Ø¹Ù„ÙŠ Ø¹Ù…Ø± Ø¹Ù„ÙŠ Ø§Ø¨Ùˆ Ù‡Ù†ÙŠØ©'],
            ];
            
            $searchNames = [$houseHolder, $cleanedHouseHolder];
            if (isset($nameMap[$cleanedHouseHolder])) {
                $searchNames = array_merge($searchNames, $nameMap[$cleanedHouseHolder]);
            } elseif (isset($nameMap[$houseHolder])) {
                $searchNames = array_merge($searchNames, $nameMap[$houseHolder]);
            }
            
            // Try multiple variations of the name for better matching
            $household = Household::where(function($query) use ($searchNames, $cleanedHouseHolder) {
                foreach ($searchNames as $name) {
                    $query->orWhere('english_name', $name)
                          ->orWhere('arabic_name', $name);
                }
                
                // Try partial matches for common name variations
                if (strlen($cleanedHouseHolder) > 10) {
                    $query->orWhere('english_name', 'LIKE', '%' . substr($cleanedHouseHolder, 0, -5) . '%')
                          ->orWhere('arabic_name', 'LIKE', '%' . substr($cleanedHouseHolder, 0, -5) . '%');
                }
            })->first();
            if ($household) {
                $data['household_id'] = $household->id;
                \Log::info("Row #{$rowCount}: Found household ID {$household->id} for name: '{$houseHolder}'");
            } else {
                // If not found as household, try to find as public structure
                $publicStructure = \App\Models\PublicStructure::where(function($query) use ($searchNames, $cleanedHouseHolder) {
                    foreach ($searchNames as $name) {
                        $query->orWhere('english_name', $name)
                              ->orWhere('arabic_name', $name);
                    }
                    
                    // Try partial matches for common name variations
                    if (strlen($cleanedHouseHolder) > 10) {
                        $query->orWhere('english_name', 'LIKE', '%' . substr($cleanedHouseHolder, 0, -5) . '%')
                              ->orWhere('arabic_name', 'LIKE', '%' . substr($cleanedHouseHolder, 0, -5) . '%');
                    }
                })->first();
                if ($publicStructure) {
                    $data['publicstructure_id'] = $publicStructure->id;
                    \Log::info("Row #{$rowCount}: Found public structure ID {$publicStructure->id} for name: '{$houseHolder}'");
                } else {
                    // Last resort: try to find household by comet_id if available
                    if (!empty($cometId)) {
                        $householdByComet = Household::where('comet_id', $cometId)->first();
                        if ($householdByComet) {
                            $data['household_id'] = $householdByComet->id;
                            \Log::info("Row #{$rowCount}: Found household ID {$householdByComet->id} using Comet ID: '{$cometId}'");
                        } else {
                            \Log::warning("Row #{$rowCount}: Main Holder not found as household or public structure in database for name: '{$houseHolder}' or household comet_id: '{$cometId}'");
                            // Create a PublicStructure from the Excel text so we can link the meter_history
                            try {
                                // Ensure community exists
                                $communityIdForPublic = $data['community_id'] ?? null;
                                if (is_null($communityIdForPublic)) {
                                    $fallbackCommunity = Community::firstOrCreate(
                                        ['english_name' => 'Unknown Community'],
                                        ['arabic_name' => null]
                                    );
                                    $communityIdForPublic = $fallbackCommunity->id;
                                    \Log::warning("Row #{$rowCount}: community_id was null — using fallback Community ID {$communityIdForPublic} when creating PublicStructure");
                                }

                                $createdPublic = PublicStructure::create([
                                    'english_name' => $cleanedHouseHolder,
                                    'arabic_name' => null,
                                    'community_id' => $communityIdForPublic
                                ]);
                                $data['publicstructure_id'] = $createdPublic->id;
                                \Log::warning("Row #{$rowCount}: Created PublicStructure '{$cleanedHouseHolder}' with ID {$createdPublic->id} from Excel householder text");
                            } catch (\Exception $e) {
                                \Log::error("Row #{$rowCount}: Failed to create PublicStructure for '{$cleanedHouseHolder}': " . $e->getMessage());
                            }
                        }
                    } else {
                        \Log::warning("Row #{$rowCount}: Main Holder not found as household or public structure in database for name: '{$houseHolder}'");
                        // If no cometId, attempt to create PublicStructure from Excel text
                        try {
                            $communityIdForPublic = $data['community_id'] ?? null;
                            if (is_null($communityIdForPublic)) {
                                $fallbackCommunity = Community::firstOrCreate(
                                    ['english_name' => 'Unknown Community'],
                                    ['arabic_name' => null]
                                );
                                $communityIdForPublic = $fallbackCommunity->id;
                                \Log::warning("Row #{$rowCount}: community_id was null — using fallback Community ID {$communityIdForPublic} when creating PublicStructure");
                            }

                            $createdPublic = PublicStructure::create([
                                'english_name' => $cleanedHouseHolder,
                                'arabic_name' => null,
                                'community_id' => $communityIdForPublic
                            ]);
                            $data['publicstructure_id'] = $createdPublic->id;
                            \Log::warning("Row #{$rowCount}: Created PublicStructure '{$cleanedHouseHolder}' with ID {$createdPublic->id} from Excel householder text");
                        } catch (\Exception $e) {
                            \Log::error("Row #{$rowCount}: Failed to create PublicStructure for '{$cleanedHouseHolder}': " . $e->getMessage());
                        }
                    }
                }
            }
        } else {
            // If householder name is empty, try to find household by comet_id
            if (!empty($cometId)) {
                $householdByComet = Household::where('comet_id', $cometId)->first();
                if ($householdByComet) {
                    $data['household_id'] = $householdByComet->id;
                    \Log::info("Row #{$rowCount}: Found household ID {$householdByComet->id} using Comet ID: '{$cometId}' (empty householder name)");
                } else {
                    \Log::warning("Row #{$rowCount}: HouseHolder column is empty and no household found with comet_id: '{$cometId}'");
                }
            } else {
                \Log::warning("Row #{$rowCount}: HouseHolder column is empty and no comet_id provided");
            }
        }

        // Find community - prioritize old_community column
        if (!empty($oldCommunity)) {
            // Handle community name variations
            $communityMap = [
                'Deir' => ['Deir Abu Mashal', 'Dayr', 'Ad Deir'],
            ];
            
            $searchCommunities = [$oldCommunity];
            if (isset($communityMap[$oldCommunity])) {
                $searchCommunities = array_merge($searchCommunities, $communityMap[$oldCommunity]);
            }
            
            $communityRecord = Community::where(function($query) use ($searchCommunities) {
                foreach ($searchCommunities as $name) {
                    $query->orWhere('english_name', $name)
                          ->orWhere('arabic_name', $name);
                }
                // Try partial match for community names
                $query->orWhere('english_name', 'LIKE', '%' . $searchCommunities[0] . '%')
                      ->orWhere('arabic_name', 'LIKE', '%' . $searchCommunities[0] . '%');
            })->first();
            
            if ($communityRecord) {
                $data['community_id'] = $communityRecord->id;
                \Log::info("Row #{$rowCount}: Found community ID {$communityRecord->id} for old community name: '{$oldCommunity}'");
            } else {
                \Log::warning("Row #{$rowCount}: Old community not found in database for name: '{$oldCommunity}' (tried variations)");
            }
        } elseif (!empty($community)) {
            $communityRecord = Community::where('english_name', $community)
                ->orWhere('arabic_name', $community)
                ->first();
            if ($communityRecord) {
                $data['community_id'] = $communityRecord->id;
                \Log::info("Row #{$rowCount}: Found community ID {$communityRecord->id} for community name: '{$community}'");
            } else {
                \Log::warning("Row #{$rowCount}: Community not found in database for name: '{$community}'");
            }
        }

        // Parse change date
        if (!empty($changeDate)) {
            try {
                if (is_numeric($changeDate)) {
                    // Handle Excel serial date
                    $date = ExcelDate::excelToDateTimeObject($changeDate);
                    $data['date'] = $date->format('Y-m-d');
                } else {
                    // Handle string date
                    $date = Carbon::parse($changeDate);
                    $data['date'] = $date->format('Y-m-d');
                }
            } catch (Exception $e) {
                \Log::warning("Could not parse date: {$changeDate}");
            }
        }

        // Direct field mappings
        // old_meter_number is separate from main_energy_meter - use Excel value directly
        if (!empty($oldMeterNumber)) {
            $data['old_meter_number'] = $oldMeterNumber;
        } else {
            // If old_meter_number is empty in Excel, use a default or the main meter as fallback
            $data['old_meter_number'] = !empty($mainEnergyMeter) ? $mainEnergyMeter : 'N/A';
        }
        
        if (!empty($newMeterNumber)) {
            $data['new_meter_number'] = $newMeterNumber;
            \Log::info("Row #{$rowCount}: Storing new meter number (if replaced): '{$newMeterNumber}'");
        } else {
            \Log::info("Row #{$rowCount}: No new meter number provided (new_meter_number_if_replaced is empty)");
        }
        
        if (!empty($householdStatus)) {
            $data['household_status'] = $householdStatus;
        }
        
        if (!empty($newHolderStatus)) {
            $data['new_holder_status'] = $newHolderStatus;
        }
        
        if (!empty($notes)) {
            $data['notes'] = $notes;
        }

        // Find shared user - currently only supports households
        if (!empty($sharedUser)) {
            $sharedHousehold = Household::where('english_name', $sharedUser)
                ->orWhere('arabic_name', $sharedUser)
                ->first();
            $energyUserId = $data['all_energy_meter_id'] ?? $data['main_energy_meter_id'] ?? null;
            if ($sharedHousehold) {
                // Find existing HouseholdMeter linking this household/public to the energy user
                $householdMeter = \App\Models\HouseholdMeter::where('household_id', $sharedHousehold->id)
                    ->when($energyUserId, function($q) use ($energyUserId) { return $q->where('energy_user_id', $energyUserId); })
                    ->where('is_archived', 0)
                    ->first();
                if ($householdMeter) {
                    // shared_user_id should reference the household_meters table id
                    $data['shared_user_id'] = $householdMeter->id;
                    \Log::info("Row #{$rowCount}: Found HouseholdMeter ID {$householdMeter->id} for shared household '{$sharedUser}' - storing householdMeter id in shared_user_id");
                } else {
                    // Create household_meter record so FK can reference it
                    try {
                        $hm = new \App\Models\HouseholdMeter();
                        $hm->energy_user_id = $energyUserId;
                        $hm->household_id = $sharedHousehold->id;
                        $hm->is_archived = 0;
                        $hm->household_name = $sharedHousehold->english_name ?? null;
                        $hm->user_name = $sharedHousehold->english_name ?? null;
                        $hm->save();
                        $data['shared_user_id'] = $hm->id;
                        \Log::warning("Row #{$rowCount}: Created HouseholdMeter ID {$hm->id} for shared household '{$sharedUser}' - storing householdMeter id in shared_user_id");
                    } catch (\Exception $e) {
                        \Log::error("Row #{$rowCount}: Failed to create HouseholdMeter for shared household '{$sharedUser}': " . $e->getMessage());
                    }
                }
            } else {
                \Log::warning("Row #{$rowCount}: Shared user not found as household in database for name: '{$sharedUser}'");
            }
        }

        // Find new household - first try household, then public structure if not found
        if (!empty($newHousehold)) {
            $cleanedNewHousehold = preg_replace('/\s+/', ' ', trim($newHousehold));
            // Try multiple variations of the name for better matching
            $newHouseholdRecord = Household::where(function($query) use ($newHousehold, $cleanedNewHousehold) {
                $query->where('english_name', $cleanedNewHousehold)
                      ->orWhere('arabic_name', $cleanedNewHousehold)
                      ->orWhere('english_name', $newHousehold)
                      ->orWhere('arabic_name', $newHousehold);
                
                // Try partial matches for common name variations
                if (strlen($cleanedNewHousehold) > 10) {
                    $query->orWhere('english_name', 'LIKE', '%' . substr($cleanedNewHousehold, 0, -5) . '%')
                          ->orWhere('arabic_name', 'LIKE', '%' . substr($cleanedNewHousehold, 0, -5) . '%');
                }
            })->first();
            
            if ($newHouseholdRecord) {
                $data['new_household_id'] = $newHouseholdRecord->id;
                \Log::info("Row #{$rowCount}: Found new household ID {$newHouseholdRecord->id} for name: '{$newHousehold}'");
            } else {
                // If not found as household, try to find as public structure
                $newPublicStructure = \App\Models\PublicStructure::where(function($query) use ($newHousehold, $cleanedNewHousehold) {
                    $query->where('english_name', $cleanedNewHousehold)
                          ->orWhere('arabic_name', $cleanedNewHousehold)
                          ->orWhere('english_name', $newHousehold)
                          ->orWhere('arabic_name', $newHousehold);
                    
                    // Try partial matches for common name variations
                    if (strlen($cleanedNewHousehold) > 10) {
                        $query->orWhere('english_name', 'LIKE', '%' . substr($cleanedNewHousehold, 0, -5) . '%')
                              ->orWhere('arabic_name', 'LIKE', '%' . substr($cleanedNewHousehold, 0, -5) . '%');
                    }
                })->first();
                
                if ($newPublicStructure) {
                    $data['new_public_structure_id'] = $newPublicStructure->id;
                    \Log::info("Row #{$rowCount}: Found new public structure ID {$newPublicStructure->id} for name: '{$newHousehold}'");
                } else {
                    // Try more aggressive matching strategies
                    \Log::warning("Row #{$rowCount}: CRITICAL - New household '{$newHousehold}' must be assigned but not found in database");
                    
                    // Try word-by-word search for household
                    $words = explode(' ', $cleanedNewHousehold);
                    if (count($words) >= 2) {
                        $firstTwoWords = $words[0] . ' ' . $words[1];
                        $fallbackHousehold = Household::where('english_name', 'LIKE', '%' . $firstTwoWords . '%')
                            ->orWhere('arabic_name', 'LIKE', '%' . $firstTwoWords . '%')
                            ->first();
                        if ($fallbackHousehold) {
                            $data['new_household_id'] = $fallbackHousehold->id;
                            \Log::warning("Row #{$rowCount}: Found new household ID {$fallbackHousehold->id} using partial name match: '{$firstTwoWords}' -> '{$fallbackHousehold->english_name}'");
                        } else {
                            // Try for public structure with partial match
                            $fallbackPublicStructure = \App\Models\PublicStructure::where('english_name', 'LIKE', '%' . $firstTwoWords . '%')
                                ->orWhere('arabic_name', 'LIKE', '%' . $firstTwoWords . '%')
                                ->first();
                            if ($fallbackPublicStructure) {
                                $data['new_public_structure_id'] = $fallbackPublicStructure->id;
                                \Log::warning("Row #{$rowCount}: Found new public structure ID {$fallbackPublicStructure->id} using partial name match: '{$firstTwoWords}' -> '{$fallbackPublicStructure->english_name}'");
                            } else {
                                \Log::error("Row #{$rowCount}: FAILED TO ASSIGN - New household '{$newHousehold}' not found even with aggressive matching! Attempting to create PublicStructure from Excel text.");
                                try {
                                    $communityIdForNewPublic = $data['community_id'] ?? null;
                                    if (is_null($communityIdForNewPublic)) {
                                        $fallbackCommunity = Community::firstOrCreate(
                                            ['english_name' => 'Unknown Community'],
                                            ['arabic_name' => null]
                                        );
                                        $communityIdForNewPublic = $fallbackCommunity->id;
                                    }
                                    $createdNewPublic = PublicStructure::create([
                                        'english_name' => $cleanedNewHousehold,
                                        'arabic_name' => null,
                                        'community_id' => $communityIdForNewPublic
                                    ]);
                                    $data['new_public_structure_id'] = $createdNewPublic->id;
                                    \Log::warning("Row #{$rowCount}: Created PublicStructure '{$cleanedNewHousehold}' with ID {$createdNewPublic->id} for new_household");
                                } catch (\Exception $e) {
                                    \Log::error("Row #{$rowCount}: Failed to create PublicStructure for new_household '{$cleanedNewHousehold}': " . $e->getMessage());
                                }
                            }
                        }
                    } else {
                        \Log::error("Row #{$rowCount}: FAILED TO ASSIGN - New household '{$newHousehold}' is too short for partial matching!");
                    }
                }
            }
        } else {
            // Check if this is a "Used by other" status that should have new household data
            if ($statusRecord && stripos($statusRecord->english_name, 'used by other') !== false) {
                \Log::error("Row #{$rowCount}: CRITICAL ERROR - 'Used by other' status requires new household data but new_household field is empty!");
            } else {
                \Log::info("Row #{$rowCount}: No new household specified (not required for this status)");
            }
        }

        // Find new community
        if (!empty($newCommunity)) {
            $newCommunityRecord = Community::where('english_name', $newCommunity)
                ->orWhere('arabic_name', $newCommunity)
                ->first();
            if ($newCommunityRecord) {
                $data['new_community_id'] = $newCommunityRecord->id;
            }
        }

        // Check for EXACT duplicate - compare ALL fields
        // If any field is different, it's considered a new/updated record for the same meter history
        $duplicateQuery = MeterHistories::query();
        
        // Add all fields to the duplicate check
        foreach ($data as $field => $value) {
            if ($value === null) {
                $duplicateQuery->whereNull($field);
            } else {
                $duplicateQuery->where($field, $value);
            }
        }
        
        $existingRecord = $duplicateQuery->first();

        if ($existingRecord) {
            \Log::warning("Row #{$rowCount}: EXACT duplicate found - all fields match existing record (ID: {$existingRecord->id}) - skipping");
            self::$skipped++;
            return null;
        } else {
            // Check if there's a record with same old_meter_number but different fields
            if (isset($data['old_meter_number'])) {
                $similarRecord = MeterHistories::where('old_meter_number', $data['old_meter_number'])->first();
                if ($similarRecord) {
                    \Log::info("Row #{$rowCount}: Found existing record with same old_meter_number '{$data['old_meter_number']}' but different fields - treating as update/new entry");
                }
            }
        }

        // Final validation for "Used by other" status
        if ($statusRecord && stripos($statusRecord->english_name, 'used by other') !== false) {
            if (!empty($newHousehold) && !isset($data['new_household_id']) && !isset($data['new_public_structure_id'])) {
                \Log::error("Row #{$rowCount}: VALIDATION FAILED - 'Used by other' status with new household '{$newHousehold}' but no new_household_id or new_public_structure_id assigned!");
            } elseif (!empty($newHousehold) && (isset($data['new_household_id']) || isset($data['new_public_structure_id']))) {
                \Log::info("Row #{$rowCount}: VALIDATION PASSED - 'Used by other' status has required new household ID assigned");
            }
        }

        // Ensure community_id is set. Try to derive from linked AllEnergyMeter, otherwise create/reuse a fallback Community.
        if (empty($data['community_id'])) {
            $derivedCommunity = null;
            $aeId = $data['all_energy_meter_id'] ?? $data['main_energy_meter_id'] ?? null;
            if ($aeId) {
                try {
                    $ae = AllEnergyMeter::find($aeId);
                    if ($ae && !empty($ae->community_id)) {
                        $derivedCommunity = $ae->community_id;
                        $data['community_id'] = $derivedCommunity;
                        \Log::info("Row #{$rowCount}: Derived community_id {$derivedCommunity} from AllEnergyMeter ID {$aeId}");
                    }
                } catch (\Exception $e) {
                    \Log::warning("Row #{$rowCount}: Failed to lookup AllEnergyMeter {$aeId} for community: " . $e->getMessage());
                }
            }

            if (empty($data['community_id'])) {
                $fallbackCommunity = Community::firstOrCreate(
                    ['english_name' => 'Unknown Community'],
                    ['arabic_name' => null]
                );
                $data['community_id'] = $fallbackCommunity->id;
                \Log::warning("Row #{$rowCount}: community_id was null — using fallback Community ID {$fallbackCommunity->id}");
            }
        }

        // Ensure publicstructure_id is a valid existing id — create or reuse fallback PublicStructure when missing
        if (empty($data['publicstructure_id'])) {
            try {
                $communityIdForPublic = $data['community_id'] ?? null;
                if (is_null($communityIdForPublic)) {
                    $fallbackCommunity = Community::firstOrCreate(
                        ['english_name' => 'Unknown Community'],
                        ['arabic_name' => null]
                    );
                    $communityIdForPublic = $fallbackCommunity->id;
                    \Log::warning("Row #{$rowCount}: community_id was null — using fallback Community ID {$communityIdForPublic} for final PublicStructure fallback");
                }

                $fallbackPublic = PublicStructure::firstOrCreate(
                    ['english_name' => 'Unknown Public Structure', 'community_id' => $communityIdForPublic],
                    ['arabic_name' => null]
                );
                $data['publicstructure_id'] = $fallbackPublic->id;
                \Log::warning("Row #{$rowCount}: publicstructure_id was missing — using fallback PublicStructure ID {$fallbackPublic->id} (community_id={$communityIdForPublic})");
            } catch (\Exception $e) {
                \Log::error("Row #{$rowCount}: Failed to ensure fallback PublicStructure: " . $e->getMessage());
            }
        }

            // Final safeguard: ensure main/all energy meter IDs exist to satisfy FK constraints
            if (empty($data['main_energy_meter_id']) || empty($data['all_energy_meter_id'])) {
                try {
                    $meterIdentifier = !empty($mainEnergyMeter) ? $mainEnergyMeter : (!empty($oldMeterNumber) ? $oldMeterNumber : ('unknown_meter_' . $rowCount . '_' . time()));
                    $existing = AllEnergyMeter::where('meter_number', $meterIdentifier)->first();
                    if ($existing) {
                        $data['all_energy_meter_id'] = $existing->id;
                        $data['main_energy_meter_id'] = $existing->id;
                        \Log::info("Row #{$rowCount}: Final safeguard assigned existing AllEnergyMeter ID {$existing->id} for identifier {$meterIdentifier}");
                    } else {
                        $fallbackMeter = new AllEnergyMeter();
                        $fallbackMeter->meter_number = $meterIdentifier;
                        if (!empty($data['household_id'])) $fallbackMeter->household_id = $data['household_id'];
                        if (!empty($data['publicstructure_id'])) $fallbackMeter->public_structure_id = $data['publicstructure_id'];
                        if (!empty($data['community_id'])) $fallbackMeter->community_id = $data['community_id'];
                        $fallbackMeter->is_archived = 0;
                        $fallbackMeter->is_main = 'No';
                        $fallbackMeter->meter_case_id = $fallbackMeter->meter_case_id ?? 1;
                        $fallbackMeter->installation_type_id = $fallbackMeter->installation_type_id ?? 4;
                        $fallbackMeter->save();
                        $data['all_energy_meter_id'] = $fallbackMeter->id;
                        $data['main_energy_meter_id'] = $fallbackMeter->id;
                        \Log::warning("Row #{$rowCount}: Final safeguard created AllEnergyMeter ID {$fallbackMeter->id} for identifier {$meterIdentifier}");
                    }
                } catch (\Exception $e) {
                    \Log::error("Row #{$rowCount}: Final safeguard failed to create AllEnergyMeter: " . $e->getMessage());
                }
            }

        // Log the data being inserted for debugging
        $dataFields = array_filter($data, function($value) { return !is_null($value); });
        \Log::info("Row #{$rowCount}: Creating new meter history record with fields: " . json_encode($dataFields));

        self::$inserted++;
        return new MeterHistories($data);
    }
}
