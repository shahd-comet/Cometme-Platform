<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeterHistories;
use App\Models\AllEnergyMeter;
use App\Models\Household;
use App\Models\PublicStructure;
use App\Models\HouseholdMeter;
use App\Helpers\SequenceHelper;

class MeterHistoryAllController extends Controller
{
    public function index(Request $request) 
    {
        $query = MeterHistories::with([
            'reason', 'status', 'community', 'household', 'publicstructure',
            'newHousehold', 'newCommunity', 'newPublicStructure', 'mainEnergyMeter', 'sharedUser'
        ]);

        // Preserve filters for view
        $filters = $request->only(['meter_number', 'community_id', 'household_id', 'status_id', 'reason_id']);

        // Status / Reason filters
        if ($request->filled('status_id')) {
            $query->where('meter_history_status_id', $request->input('status_id'));
        }
        if ($request->filled('reason_id')) {
            $query->where('meter_history_reason_id', $request->input('reason_id'));
        }

        // Meter number search: old, new, mainEnergyMeter (meter_number, fake_meter_number)
        if ($request->filled('meter_number')) {
            $meter = trim($request->input('meter_number'));
            $query->where(function ($q) use ($meter) {
                $q->where('old_meter_number', 'like', "%{$meter}%")
                  ->orWhere('new_meter_number', 'like', "%{$meter}%")
                  ->orWhereHas('mainEnergyMeter', function ($mQ) use ($meter) {
                      $mQ->where('meter_number', 'like', "%{$meter}%")
                         ->orWhere('fake_meter_number', 'like', "%{$meter}%");
                  });
            });
        }

        // Household filter: direct or via relations (household, shared_user, mainEnergyMeter holder)
        if ($request->filled('household_id')) {
            $hid = $request->input('household_id');
            $query->where(function ($q) use ($hid) {
                $q->where('household_id', $hid)
                  ->orWhere('shared_user_id', $hid)
                  ->orWhereHas('mainEnergyMeter', function ($mq) use ($hid) {
                      $mq->where('household_id', $hid)
                         ->orWhere('public_structure_id', $hid);
                  })
                  ->orWhereHas('household', function ($hq) use ($hid) {
                      $hq->where('id', $hid);
                  });
            });
        }

        // Community filter: direct or via relations
        if ($request->filled('community_id')) {
            $cid = $request->input('community_id');
            $query->where(function ($q) use ($cid) {
                $q->where('community_id', $cid)
                  ->orWhere('new_community_id', $cid)
                  ->orWhereHas('mainEnergyMeter', function ($mq) use ($cid) {
                      $mq->where('community_id', $cid);
                  })
                  ->orWhereHas('community', function ($cq) use ($cid) {
                      $cq->where('id', $cid);
                  });
            });
        }

        // Pagination and preserve query string
        $meterHistories = $query->orderByDesc('date')->paginate(20)->appends($request->except('page'));

        return view('meter-history.all', compact('meterHistories', 'filters'));
    }

    public function byMeterNumber($meter_number)
    {
        $histories = MeterHistories::with([
            'reason', 'status', 'community', 'household', 'publicstructure',
            'newHousehold', 'newCommunity', 'newPublicStructure', 'mainEnergyMeter', 'sharedUser'
        ])
        ->where('old_meter_number', $meter_number)
        ->orWhereHas('mainEnergyMeter', function($q) use ($meter_number) {
            $q->where('meter_number', $meter_number);
        })
        ->get();

        if ($histories->isEmpty()) {
            return response()->json(['empty' => true]);
        }

        // Try to find the meter record and current holder info
        $meter = \App\Models\AllEnergyMeter::where('meter_number', $meter_number)->first();

        $currentHolder = null;
        $communityName = null;
        $meterStatusLabel = null; // e.g., 'used by other'
        $currentReason = null;
        $currentStatus = null;
        $currentPublicStructure = null;
        $currentHousehold = null;
        $currentSharedUser = null;

        if ($meter) {
            if ($meter->Household) {
                $currentHolder = $meter->Household->english_name;
                $currentHousehold = $meter->Household;
            } elseif ($meter->PublicStructure) {
                $currentHolder = $meter->PublicStructure->english_name;
                $currentPublicStructure = $meter->PublicStructure;
            }
            if ($meter->Community) {
                $communityName = $meter->Community->english_name;
            }
            if ($meter->MeterCase) {
                $meterStatusLabel = $meter->MeterCase->meter_case_name_english ?? null;
            }
            if ($meter->PublicStructure && !$currentPublicStructure) {
                $currentPublicStructure = $meter->PublicStructure;
            }
        }

        // Latest history record for summary (by date or id)
        $latest = $histories->sortByDesc('date')->first() ?? $histories->sortByDesc('id')->first();

        // Get latest history record to extract current reason and status
        if ($latest) {
            if ($latest->reason) {
                $currentReason = $latest->reason;
            }
            if ($latest->status) {
                $currentStatus = $latest->status;
            }
            if ($latest->sharedUser) {
                $currentSharedUser = $latest->sharedUser;
            }
        }

        return view('meter-history._modal', compact(
            'histories', 
            'meter', 
            'currentHolder', 
            'communityName', 
            'latest',
            'meterStatusLabel',
            'currentReason',
            'currentStatus',
            'currentPublicStructure',
            'currentHousehold',
            'currentSharedUser'
        ))->render();
    }

    public function show($meter_number)
    {
        // Log the meter search for debugging
        \Log::info("Searching for meter history", [
            'meter_number' => $meter_number,
            'timestamp' => now()
        ]);

        // Search for histories by meter number in both old_meter_number and mainEnergyMeter relationship
        $histories = MeterHistories::with([
            'reason', 'status', 'community', 'household', 'publicstructure',
            'newHousehold', 'newCommunity', 'newPublicStructure', 'mainEnergyMeter', 'sharedUser'
        ])
        ->where(function($query) use ($meter_number) {
            $query->where('old_meter_number', $meter_number)
                  ->orWhereHas('mainEnergyMeter', function($q) use ($meter_number) {
                      $q->where('meter_number', $meter_number);
                  });
        })
        ->orderByDesc('date')
        ->get();

        \Log::info("Meter history search results", [
            'meter_number' => $meter_number,
            'histories_found' => $histories->count(),
            'history_ids' => $histories->pluck('id')->toArray()
        ]);

        // Try to find the meter record
        $meter = \App\Models\AllEnergyMeter::where('meter_number', $meter_number)->first();

        // If no meter found and no histories, show error
        if (!$meter && $histories->isEmpty()) {
            return redirect()->route('meter-history.all')
                ->with('error', "Meter '{$meter_number}' not found in the system.");
        }

        $currentHolder = null;
        $communityName = null;
        $meterStatus = null;
        $currentReasonId = null;
        $currentReason = null;
        $currentStatus = null;
        $currentPublicStructure = null;
        $currentHousehold = null;
        $currentSharedUser = null;

        if ($meter) {
            if ($meter->Household) {
                $currentHolder = $meter->Household->english_name;
                $currentHousehold = $meter->Household;
            } elseif ($meter->PublicStructure) {
                $currentHolder = $meter->PublicStructure->english_name;
                $currentPublicStructure = $meter->PublicStructure;
            }
            
            if ($meter->Community) {
                $communityName = $meter->Community->english_name;
            }
            if ($meter->MeterCase) {
                $meterStatus = $meter->MeterCase->meter_case_name_english ?? null;
            }
            if ($meter->PublicStructure && !$currentPublicStructure) {
                $currentPublicStructure = $meter->PublicStructure;
            }
        }

        // Latest history record for summary
        $latest = $histories->first();

        // Get current reason and status from latest history
        if ($latest) {
            if ($latest->reason) {
                $currentReason = $latest->reason;
                $currentReasonId = $latest->reason->id;
            }
            if ($latest->status) {
                $currentStatus = $latest->status;
            }
            if ($latest->sharedUser) {
                $currentSharedUser = $latest->sharedUser;
            }
        }

        // Purchase dates (from meter table if available)
        $purchaseDates = [];
        if ($meter) {
            if ($meter->last_purchase_date_1) $purchaseDates[] = $meter->last_purchase_date_1;
            if ($meter->last_purchase_date_2) $purchaseDates[] = $meter->last_purchase_date_2;
            if ($meter->last_purchase_date_3) $purchaseDates[] = $meter->last_purchase_date_3;
        }

        return view('meter-history.show', compact(
            'meter_number', 
            'histories', 
            'meter', 
            'currentHolder', 
            'communityName', 
            'meterStatus',
            'latest',
            'purchaseDates',
            'currentReasonId',
            'currentReason',
            'currentStatus',
            'currentPublicStructure',
            'currentHousehold',
            'currentSharedUser'
        ));
    }

    /**
     * Get a specific history record for editing
     */
    public function edit($id)
    {
        $history = MeterHistories::with([
            'reason', 'status', 'community', 'household', 'publicstructure',
            'newHousehold', 'newCommunity', 'newPublicStructure', 'mainEnergyMeter', 'sharedUser'
        ])->findOrFail($id);

        return response()->json($history);
    }

    /**
     * Update a specific history record
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'date' => 'required|date|before_or_equal:today',
                'meter_history_status_id' => 'required|exists:meter_history_statuses,id',
                'meter_history_reason_id' => 'nullable|exists:meter_history_reasons,id',
                'community_id' => 'nullable|exists:communities,id',
                'household_id' => 'nullable|exists:households,id',
                'new_household_id' => 'nullable|exists:households,id|different:household_id',
                'new_community_id' => 'nullable|exists:communities,id',
                'shared_user_id' => 'nullable|exists:households,id',
                'household_status' => 'nullable|string|max:255',
                'new_holder_status' => 'nullable|string|max:255',
                'old_meter_number' => 'nullable|string|max:255',
                'new_meter_number' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ], [
                'date.required' => 'The date field is required.',
                'date.date' => 'Please provide a valid date.',
                'date.before_or_equal' => 'The date cannot be in the future.',
                'meter_history_status_id.required' => 'Please select a status.',
                'meter_history_status_id.exists' => 'The selected status is invalid.',
                'new_household_id.different' => 'New household must be different from the main household.',
                'household_id.exists' => 'The selected household does not exist.',
                'community_id.exists' => 'The selected community does not exist.',
                'notes.max' => 'Notes cannot exceed 1000 characters.',
            ]);

            $history = MeterHistories::findOrFail($id);
            
            // Additional business logic validation
            $this->validateBusinessRules($validatedData, $history);
            
            // Update the record
            $history->update($validatedData);

            // Log the update for audit purposes
            \Log::info("History record updated", [
                'history_id' => $history->id,
                'updated_by' => auth()->user()->id ?? 'system',
                'changes' => $validatedData
            ]);

            // Check if this is an AJAX request or regular form submission
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'History record updated successfully',
                    'data' => $history->fresh()->load([
                        'reason', 'status', 'community', 'household', 'publicstructure',
                        'newHousehold', 'newCommunity', 'newPublicStructure', 'mainEnergyMeter', 'sharedUser'
                    ])
                ]);
            } else {
                // Regular form submission - redirect back to the meter history show page
                $meterNumber = $history->old_meter_number ?? $history->mainEnergyMeter->meter_number ?? 'unknown';
                return redirect()->route('meter-history.show', $meterNumber)
                    ->with('success', 'History record updated successfully');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            } else {
                // Regular form submission - redirect back with errors
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            }

        } catch (\Exception $e) {
            \Log::error("Error updating history record", [
                'history_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the record. Please try again.'
                ], 500);
            } else {
                // Regular form submission - redirect back with error message
                return redirect()->back()
                    ->with('error', 'An error occurred while updating the record. Please try again.')
                    ->withInput();
            }
        }
    }

    /**
     * Validate business rules for history updates
     */
    private function validateBusinessRules($data, $history)
    {
        $errors = [];

        // Rule: If status indicates replacement, new_meter_number should be provided
        if (isset($data['meter_history_status_id'])) {
            $status = \App\Models\MeterHistoryStatuses::find($data['meter_history_status_id']);
            if ($status && stripos($status->english_name, 'replaced') !== false) {
                if (empty($data['new_meter_number'])) {
                    $errors['new_meter_number'] = ['New meter number is required when status indicates replacement.'];
                }
            }
        }

        // Rule: If status indicates shared, shared_user_id should be provided
        if (isset($data['meter_history_status_id'])) {
            $status = \App\Models\MeterHistoryStatuses::find($data['meter_history_status_id']);
            if ($status && (stripos($status->english_name, 'shared') !== false || stripos($status->english_name, 'become a shared') !== false)) {
                if (empty($data['shared_user_id'])) {
                    $errors['shared_user_id'] = ['Shared user is required when status indicates sharing.'];
                }
            }
        }

        // Rule: If status indicates relocation/transfer, new_community_id should be provided
        if (isset($data['meter_history_status_id'])) {
            $status = \App\Models\MeterHistoryStatuses::find($data['meter_history_status_id']);
            if ($status && (stripos($status->english_name, 'relocated') !== false || stripos($status->english_name, 'transfer') !== false)) {
                if (empty($data['new_community_id'])) {
                    $errors['new_community_id'] = ['New community is required when status indicates relocation or transfer.'];
                }
            }
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    /**
     * Delete a specific history record
     */
    public function destroy($id)
    {
        try {
            $history = MeterHistories::findOrFail($id);
            
            // Optional: Add business logic to prevent deletion of certain records
            // For example, prevent deletion of the latest record or records older than a certain date
            
            // Store info for logging before deletion
            $deletedInfo = [
                'id' => $history->id,
                'old_meter_number' => $history->old_meter_number,
                'date' => $history->date,
                'status' => $history->status->english_name ?? 'N/A'
            ];
            
            $history->delete();

            // Log the deletion for audit purposes
            \Log::info("History record deleted", [
                'deleted_record' => $deletedInfo,
                'deleted_by' => auth()->user()->id ?? 'system',
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'History record deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'History record not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error("Error deleting history record", [
                'history_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the record. Please try again.'
            ], 500);
        }
    }

    /**
     * Get households by community ID for filtering
     */
    public function getHouseholdsByCommunity($communityId)
    {
        try {
            $households = \App\Models\Household::where('community_id', $communityId)
                ->select('id', 'english_name', 'arabic_name')
                ->orderBy('english_name')
                ->get();

            return response()->json($households);

        } catch (\Exception $e) {
            \Log::error("Error fetching households for community", [
                'community_id' => $communityId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to fetch households'
            ], 500);
        }
    }

    /**
     * Create a new meter history record from the new form
     */
    public function store(Request $request)
    {
        try {
            // Validation rules for meter history creation
            $rules = [
                'date' => 'required|date|before_or_equal:today',
                'meter_history_status_id' => 'required|exists:meter_history_statuses,id',
                'meter_history_reason_id' => 'nullable|exists:meter_history_reasons,id',
                
                // Current Holder Information (required)
                'holder_type' => 'required|in:household,public',
                'current_meter_number' => 'required|string|max:255',
                'current_holder_community_id' => 'required|exists:communities,id',
                'current_holder_household_status_id' => 'required|exists:household_statuses,id',
                
                'notes' => 'nullable|string|max:1000',
            ];

            // Add conditional validation based on holder type
            $holderType = $request->input('holder_type');
            if ($holderType === 'household') {
                $rules['current_holder_household_id'] = 'required|exists:households,id';
            } elseif ($holderType === 'public') {
                $rules['current_holder_public_structure_id'] = 'required|exists:public_structures,id';
            }

            // Get the status to determine what additional fields are required
            $statusId = $request->input('meter_history_status_id');
            if ($statusId) {
                $status = \App\Models\MeterHistoryStatuses::find($statusId);
                $statusName = strtolower($status->english_name ?? '');

                // Add conditional validation based on status - all dropdown fields validate against database tables
                if (stripos($statusName, 'replaced') !== false) {
                    $rules['new_meter_number'] = 'required|string|max:255'; // New replacement meter number
                    $rules['community_id'] = 'nullable|exists:communities,id'; // ID from communities table
                    
                } elseif (stripos($statusName, 'used by other') !== false) {
                    $rules['new_user_household_id'] = 'required|exists:households,id'; // Household ID from households table
                    $rules['new_holder_community_id'] = 'required|exists:communities,id'; // ID from communities table
                    $rules['new_holder_status_id'] = 'required|exists:household_statuses,id'; // ID from household_statuses table
                    
                } elseif (stripos($statusName, 'shared') !== false || stripos($statusName, 'become shared') !== false) {
                    $rules['shared_user_household_id'] = 'nullable|exists:households,id'; // Household ID for shared user
                    $rules['shared_user_public_structure_id'] = 'nullable|exists:public_structures,id'; // Public structure ID for shared user
                    
                } elseif (stripos($statusName, 'relocated') !== false) {
                    $rules['relocated_community_id'] = 'required|exists:communities,id'; // ID from communities table
                }
            }

            // Validate the request
            $validatedData = $request->validate($rules, [
                'date.required' => 'Date is required.',
                'date.before_or_equal' => 'Date cannot be in the future.',
                'meter_history_status_id.required' => 'Status is required.',
                'meter_history_status_id.exists' => 'Selected status is invalid.',
                
                // Current Holder Information validation messages
                'holder_type.required' => 'Holder type is required.',
                'holder_type.in' => 'Holder type must be either household or public structure.',
                'current_meter_number.required' => 'Current meter number is required.',
                'current_holder_community_id.required' => 'Current holder community is required.',
                'current_holder_community_id.exists' => 'Selected community is invalid.',
                'current_holder_household_id.required' => 'Current holder household is required.',
                'current_holder_household_id.exists' => 'Selected household is invalid.',
                'current_holder_public_structure_id.required' => 'Current holder public structure is required.',
                'current_holder_public_structure_id.exists' => 'Selected public structure is invalid.',
                'current_holder_household_status_id.required' => 'Current holder household status is required.',
                'current_holder_household_status_id.exists' => 'Selected household status is invalid.',
                
                // Status-specific validation messages
                'new_meter_number.required' => 'New meter number is required for replacement status.',
                'new_user_household_id.required' => 'New user household is required when meter is used by other.',
                'new_holder_community_id.required' => 'New community is required when meter is used by other.',
                'new_holder_status_id.required' => 'New holder status is required when meter is used by other.',
                'relocated_community_id.required' => 'New community is required for relocation status.',
                
                'community_id.exists' => 'Selected community is invalid.',
                'notes.max' => 'Notes cannot exceed 1000 characters.',
            ]);

            // Look up the household status text instead of storing the ID
            $householdStatusText = null;
            if (isset($validatedData['current_holder_household_status_id'])) {
                $householdStatus = \App\Models\HouseholdStatus::find($validatedData['current_holder_household_status_id']);
                $householdStatusText = $householdStatus ? $householdStatus->status : null;
            }

            // Prepare data for the new history record
            $historyData = [
                'date' => $validatedData['date'],
                'meter_history_status_id' => $validatedData['meter_history_status_id'],
                'meter_history_reason_id' => $validatedData['meter_history_reason_id'] ?? 0, // Use 0 as default
                'old_meter_number' => $validatedData['current_meter_number'],
                'community_id' => $validatedData['current_holder_community_id'],
                'household_status' => $householdStatusText, // Store the actual status text, not the ID
                'notes' => $validatedData['notes'] ?? null,
                
                // Add default values for other required fields based on existing records
                'household_id' => 0,
                'publicstructure_id' => 0,
                'all_energy_meter_id' => 0,
                'main_energy_meter_id' => 0,
            ];

            // Set household or public structure based on holder type
            if ($validatedData['holder_type'] === 'household') {
                $historyData['household_id'] = $validatedData['current_holder_household_id'];
            } elseif ($validatedData['holder_type'] === 'public') {
                $historyData['publicstructure_id'] = $validatedData['current_holder_public_structure_id'];
            }

            // Add status-specific fields based on selected status
            $status = \App\Models\MeterHistoryStatuses::find($validatedData['meter_history_status_id']);
            $statusName = strtolower($status->english_name ?? '');

            if (stripos($statusName, 'replaced') !== false) {
                // New meter number only for "Replaced" status - stores the replacement meter
                $historyData['new_meter_number'] = $validatedData['new_meter_number'];
                // Optional: community where replacement meter is located (from dropdown)
                if (isset($validatedData['community_id'])) {
                    $historyData['new_community_id'] = $validatedData['community_id']; // From community dropdown
                }
                
                // Create new energy meter record in all_energy_meters table with the new meter number
                $currentMeter = AllEnergyMeter::where('meter_number', $validatedData['current_meter_number'])->first();
                if ($currentMeter && !empty($validatedData['new_meter_number'])) {
                    $this->createReplacedEnergyMeter($validatedData, $currentMeter);
                }
                
            } elseif (stripos($statusName, 'used by other') !== false) {
                // Store IDs from database tables selected via dropdown menus
                $historyData['new_community_id'] = $validatedData['new_holder_community_id']; // From community dropdown
                $historyData['new_household_id'] = $validatedData['new_user_household_id']; // From household dropdown
                
                // Look up new holder status text instead of storing ID
                $newHolderStatusText = null;
                if (isset($validatedData['new_holder_status_id'])) {
                    $newHolderStatus = \App\Models\HouseholdStatus::find($validatedData['new_holder_status_id']);
                    $newHolderStatusText = $newHolderStatus ? $newHolderStatus->status : null;
                }
                $historyData['new_holder_status'] = $newHolderStatusText; // Store status text, not ID
                
                // Get household name for notes
                $newUserHousehold = \App\Models\Household::find($validatedData['new_user_household_id']);
                $newUserName = $newUserHousehold ? $newUserHousehold->english_name : 'Unknown';
                
                // Store new user household name in notes
                if (empty($historyData['notes'])) {
                    $historyData['notes'] = 'New User: ' . $newUserName;
                } else {
                    $historyData['notes'] .= ' | New User: ' . $newUserName;
                }
                
            } elseif (stripos($statusName, 'shared') !== false || stripos($statusName, 'become shared') !== false) {
                // Store shared household/public structure ID and use current holder community
                if (isset($validatedData['shared_user_household_id']) || isset($validatedData['shared_user_public_structure_id'])) {
                    
                    $sharedUserEntity = null;
                    $sharedUserName = 'Unknown';
                    $sharedUserId = null;
                    
                    if (isset($validatedData['shared_user_household_id'])) {
                        $sharedUserId = $validatedData['shared_user_household_id'];
                        $historyData['shared_household_id'] = $validatedData['shared_user_household_id'];
                        $sharedUserEntity = Household::find($validatedData['shared_user_household_id']);
                        $sharedUserName = $sharedUserEntity ? $sharedUserEntity->english_name : 'Unknown Household';
                    } elseif (isset($validatedData['shared_user_public_structure_id'])) {
                        $sharedUserId = $validatedData['shared_user_public_structure_id'];
                        $historyData['shared_public_structure_id'] = $validatedData['shared_user_public_structure_id'];
                        $sharedUserEntity = PublicStructure::find($validatedData['shared_user_public_structure_id']);
                        $sharedUserName = $sharedUserEntity ? $sharedUserEntity->english_name : 'Unknown Public Structure';
                    }
                    
                    // Store the shared user ID in the shared_user_id column
                    if ($sharedUserId) {
                        $historyData['shared_user_id'] = $sharedUserId;
                    }
                    
                    // Use the same community as current holder
                    $historyData['shared_community_id'] = $validatedData['current_holder_community_id'];
                    
                    // Store shared user name in notes
                    if (empty($historyData['notes'])) {
                        $historyData['notes'] = 'Shared User: ' . $sharedUserName;
                    } else {
                        $historyData['notes'] .= ' | Shared User: ' . $sharedUserName;
                    }

                    // Create/update shared energy meter record in all_energy_meters table
                    $this->createSharedEnergyMeter($validatedData, $sharedUserEntity);
                }
                
            } elseif (stripos($statusName, 'relocated') !== false) {
                // Store new community ID from dropdown selection
                $historyData['new_community_id'] = $validatedData['relocated_community_id']; // From community dropdown
            }

            // Household ID is already validated and selected from dropdown, no need to look up again
            // The household_id is already set in $historyData from the dropdown selection

            // Log what we're about to create for debugging
            \Log::info("About to create meter history record", [
                'data' => $historyData,
                'validated_input' => $validatedData
            ]);

            // Create the new history record
            $newHistory = MeterHistories::create($historyData);

            // Log the creation for audit purposes
            \Log::info("New meter history record created successfully", [
                'history_id' => $newHistory->id,
                'meter_number' => $validatedData['current_meter_number'],
                'holder_type' => $validatedData['holder_type'],
                'household_id' => $validatedData['current_holder_household_id'] ?? null,
                'public_structure_id' => $validatedData['current_holder_public_structure_id'] ?? null,
                'status' => $status->english_name ?? 'Unknown',
                'created_by' => auth()->user()->id ?? 'system',
                'timestamp' => now()
            ]);

            return redirect()->route('meter-history.new')
                ->with('success', 'New meter history record created successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Validation failed for meter history creation", [
                'errors' => $e->errors(),
                'input' => $request->all(),
                'rules' => $rules
            ]);
            
            // Create user-friendly error message
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages[] = implode(' ', $messages);
            }
            $userFriendlyError = 'Validation failed: ' . implode(' | ', $errorMessages);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', $userFriendlyError)
                ->withInput();

        } catch (\Exception $e) {
            \Log::error("Error creating new meter history record", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input_data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the record: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Add a new status update to the meter history
     */
    public function addUpdate(Request $request)
    {
        try {
            // Validate the basic required fields
            $rules = [
                'meter_number' => 'required|string|max:255',
                'date' => 'required|date|before_or_equal:today',
                'meter_history_status_id' => 'required|exists:meter_history_statuses,id',
                'meter_history_reason_id' => 'nullable|exists:meter_history_reasons,id',
                'current_household_id' => 'nullable|exists:households,id',
                'current_community_id' => 'nullable|exists:communities,id',
                'community_id' => 'nullable|exists:communities,id',
                'notes' => 'nullable|string|max:1000',
            ];

            // Get the status to determine what additional fields are required
            $statusId = $request->input('meter_history_status_id');
            if ($statusId) {
                $status = \App\Models\MeterHistoryStatuses::find($statusId);
                $statusName = strtolower($status->english_name ?? '');

                // Add conditional validation based on status
                if (stripos($statusName, 'replaced') !== false) {
                    $rules['new_meter_number'] = 'required|string|max:255';
                } elseif (stripos($statusName, 'used by other') !== false) {
                    $rules['new_household_id'] = 'required|exists:households,id|different:current_household_id';
                    $rules['new_community_id'] = 'nullable|exists:communities,id';
                    $rules['new_holder_status'] = 'nullable|string|max:255';
                    $rules['previous_meter_number'] = 'nullable|string|max:255';
                } elseif (stripos($statusName, 'shared') !== false || stripos($statusName, 'become shared') !== false) {
                    $rules['shared_user_id'] = 'required|exists:households,id';
                } elseif (stripos($statusName, 'relocated') !== false) {
                    $rules['relocated_community_id'] = 'required|exists:communities,id|different:current_community_id';
                }
            }

            // Validate the request
            $validatedData = $request->validate($rules, [
                'meter_number.required' => 'Meter number is required.',
                'date.required' => 'Date is required.',
                'date.before_or_equal' => 'Date cannot be in the future.',
                'meter_history_status_id.required' => 'Status is required.',
                'meter_history_status_id.exists' => 'Selected status is invalid.',
                'new_meter_number.required' => 'New meter number is required for replacement status.',
                'new_household_id.required' => 'New holder is required for this status.',
                'new_household_id.different' => 'New holder must be different from current holder.',
                'shared_user_id.required' => 'Shared user is required for shared meter status.',
                'relocated_community_id.required' => 'New community is required for relocation status.',
                'relocated_community_id.different' => 'New community must be different from current community.',
            ]);

            // Find the meter record to get current info
            $meter = \App\Models\AllEnergyMeter::where('meter_number', $request->input('meter_number'))->first();
            
            // Prepare data for the new history record
            $historyData = [
                'date' => $validatedData['date'],
                'meter_history_status_id' => $validatedData['meter_history_status_id'],
                'meter_history_reason_id' => $validatedData['meter_history_reason_id'] ?? null,
                'old_meter_number' => $request->input('meter_number'),
                'notes' => $validatedData['notes'] ?? null,
                'updated_by_user_id' => auth()->user()->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Set fields based on current meter info
            if ($meter) {
                $historyData['main_energy_meter_id'] = $meter->id;
                $historyData['household_id'] = $meter->household_id;
                $historyData['community_id'] = $meter->community_id;
            } else {
                // Fallback to current values from form
                $historyData['household_id'] = $validatedData['current_household_id'];
                $historyData['community_id'] = $validatedData['current_community_id'];
            }
            
            // Override community_id if provided in the form (optional field)
            if (!empty($validatedData['community_id'])) {
                $historyData['community_id'] = $validatedData['community_id'];
            }

            // Add status-specific fields
            $status = \App\Models\MeterHistoryStatuses::find($validatedData['meter_history_status_id']);
            $statusName = strtolower($status->english_name ?? '');

            if (stripos($statusName, 'replaced') !== false) {
                $historyData['new_meter_number'] = $validatedData['new_meter_number'];
                
                // Create new energy meter record in all_energy_meters table with the new meter number
                if (!empty($validatedData['new_meter_number']) && $meter) {
                    $currentMeterData = [
                        'current_meter_number' => $request->input('meter_number'),
                        'new_meter_number' => $validatedData['new_meter_number']
                    ];
                    $this->createReplacedEnergyMeter($currentMeterData, $meter);
                }
                
            } elseif (stripos($statusName, 'used by other') !== false) {
                $historyData['new_household_id'] = $validatedData['new_household_id'];
                $historyData['new_community_id'] = $validatedData['new_community_id'] ?? null;
                $historyData['new_holder_status'] = $validatedData['new_holder_status'] ?? null;
                
                // Store previous meter number if provided
                if (!empty($validatedData['previous_meter_number'])) {
                    $historyData['new_meter_number'] = $validatedData['previous_meter_number'];
                }
                
            } elseif (stripos($statusName, 'shared') !== false || stripos($statusName, 'become shared') !== false) {
                // Store the shared user ID in the shared_user_id column
                $historyData['shared_user_id'] = $validatedData['shared_user_id'];
                
                // Create/update shared energy meter record in all_energy_meters table
                if (!empty($validatedData['shared_user_id'])) {
                    // Try to find as household first, then as public structure
                    $sharedUserEntity = Household::find($validatedData['shared_user_id']);
                    $sharedValidatedData = [
                        'current_meter_number' => $request->input('meter_number'),
                        'current_holder_community_id' => $validatedData['current_community_id'] ?? $meter->community_id ?? null
                    ];
                    
                    if ($sharedUserEntity) {
                        $sharedValidatedData['shared_user_household_id'] = $validatedData['shared_user_id'];
                    } else {
                        // Try as public structure
                        $sharedUserEntity = PublicStructure::find($validatedData['shared_user_id']);
                        if ($sharedUserEntity) {
                            $sharedValidatedData['shared_user_public_structure_id'] = $validatedData['shared_user_id'];
                        }
                    }
                    
                    $this->createSharedEnergyMeter($sharedValidatedData, $sharedUserEntity);
                }
                
            } elseif (stripos($statusName, 'relocated') !== false) {
                $historyData['new_community_id'] = $validatedData['relocated_community_id'];
            }

            // Create the new history record
            $newHistory = MeterHistories::create($historyData);

            // Log the creation for audit purposes
            \Log::info("New meter history update added", [
                'history_id' => $newHistory->id,
                'meter_number' => $request->input('meter_number'),
                'status' => $status->english_name ?? 'Unknown',
                'created_by' => auth()->user()->id ?? 'system',
                'timestamp' => now()
            ]);

            // Load the complete record with relationships for response
            $newHistory->load([
                'reason', 'status', 'community', 'household', 'publicstructure',
                'newHousehold', 'newCommunity', 'newPublicStructure', 'mainEnergyMeter', 'sharedUser'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status update added successfully',
                'data' => $newHistory
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error("Error adding meter history update", [
                'meter_number' => $request->input('meter_number'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the update. Please try again.'
            ], 500);
        }
    }

    /**
     * Update existing energy meter record when a household/public structure becomes shared
     * or create new record if none exists
     */
    private function createSharedEnergyMeter($validatedData, $sharedUserHousehold)
    {
        try {
            // Find the main energy meter to get the main holder's information
            $mainEnergyMeter = AllEnergyMeter::where('meter_number', $validatedData['current_meter_number'])->first();
            
            if (!$mainEnergyMeter) {
                \Log::error("Main energy meter not found for shared meter creation", [
                    'meter_number' => $validatedData['current_meter_number'],
                    'shared_household_id' => $validatedData['shared_user_household_id'] ?? null
                ]);
                return false;
            }

            // Check if the shared household/public structure already has an energy meter
            $existingSharedMeter = null;
            if (isset($validatedData['shared_user_household_id'])) {
                $existingSharedMeter = AllEnergyMeter::where('household_id', $validatedData['shared_user_household_id'])
                    ->where('is_archived', 0)
                    ->first();
            } elseif (isset($validatedData['shared_user_public_structure_id'])) {
                $existingSharedMeter = AllEnergyMeter::where('public_structure_id', $validatedData['shared_user_public_structure_id'])
                    ->where('is_archived', 0)
                    ->first();
            }

            // Generate fake meter number using the main holder's meter number + 's' + sequential number
            $lastIncrementalNumber = AllEnergyMeter::whereNotNull('fake_meter_number')
                ->selectRaw('MAX(CAST(SUBSTRING_INDEX(fake_meter_number, \'s\', -1) AS UNSIGNED)) AS incremental_number')
                ->value('incremental_number');

            $lastIncrementalNumber = ($lastIncrementalNumber ?? 0) + 1;
            $newFakeMeterNumber = SequenceHelper::generateSequence($mainEnergyMeter->meter_number, $lastIncrementalNumber);

            if ($existingSharedMeter) {
                // Update existing meter record
                $existingSharedMeter->meter_number = null; // Set to NULL as required
                $existingSharedMeter->is_main = 'No'; // Set to "No" as required
                $existingSharedMeter->fake_meter_number = $newFakeMeterNumber; // Generate new fake meter number
                
                // Update other relevant properties from the main meter
                $existingSharedMeter->community_id = $validatedData['current_holder_community_id'];
                $existingSharedMeter->meter_case_id = $mainEnergyMeter->meter_case_id ?? 1;
                $existingSharedMeter->energy_system_id = $mainEnergyMeter->energy_system_id;
                $existingSharedMeter->energy_system_type_id = $mainEnergyMeter->energy_system_type_id;
                $existingSharedMeter->installation_type_id = $mainEnergyMeter->installation_type_id ?? 4;
                $existingSharedMeter->daily_limit = $mainEnergyMeter->daily_limit;
                $existingSharedMeter->vendor_username_id = $mainEnergyMeter->vendor_username_id;
                
                $existingSharedMeter->save();

                // Also create/update record in household_meters table for shared energy users system
                $this->createHouseholdMeterRecord($mainEnergyMeter, $existingSharedMeter, $validatedData);

                \Log::info("Existing energy meter updated for shared status", [
                    'updated_meter_id' => $existingSharedMeter->id,
                    'old_meter_number' => 'SET TO NULL',
                    'new_fake_meter_number' => $newFakeMeterNumber,
                    'is_main' => 'No',
                    'main_meter_number' => $mainEnergyMeter->meter_number,
                    'shared_household_id' => $validatedData['shared_user_household_id'] ?? null,
                    'shared_public_structure_id' => $validatedData['shared_user_public_structure_id'] ?? null,
                    'shared_household_name' => $sharedUserHousehold ? $sharedUserHousehold->english_name : 'Unknown'
                ]);

                return true;
            } else {
                // Create new shared energy meter record if none exists
                $sharedEnergyMeter = new AllEnergyMeter();
                
                // Set household or public structure based on what's provided
                if (isset($validatedData['shared_user_household_id'])) {
                    $sharedEnergyMeter->household_id = $validatedData['shared_user_household_id'];
                } elseif (isset($validatedData['shared_user_public_structure_id'])) {
                    $sharedEnergyMeter->public_structure_id = $validatedData['shared_user_public_structure_id'];
                }
                
                $sharedEnergyMeter->community_id = $validatedData['current_holder_community_id'];
                $sharedEnergyMeter->meter_number = null; // NULL as specified in requirements
                $sharedEnergyMeter->is_main = 'No'; // "No" as specified in requirements
                $sharedEnergyMeter->fake_meter_number = $newFakeMeterNumber; // main_holder_meter + "s" + sequential
                $sharedEnergyMeter->is_archived = 0;
                
                // Copy other relevant properties from the main meter
                $sharedEnergyMeter->meter_case_id = $mainEnergyMeter->meter_case_id ?? 1;
                $sharedEnergyMeter->energy_system_id = $mainEnergyMeter->energy_system_id;
                $sharedEnergyMeter->energy_system_type_id = $mainEnergyMeter->energy_system_type_id;
                $sharedEnergyMeter->installation_type_id = $mainEnergyMeter->installation_type_id ?? 4;
                $sharedEnergyMeter->daily_limit = $mainEnergyMeter->daily_limit;
                $sharedEnergyMeter->vendor_username_id = $mainEnergyMeter->vendor_username_id;

                $sharedEnergyMeter->save();

                // Also create record in household_meters table for shared energy users system
                $this->createHouseholdMeterRecord($mainEnergyMeter, $sharedEnergyMeter, $validatedData);

                \Log::info("New shared energy meter created successfully", [
                    'shared_meter_id' => $sharedEnergyMeter->id,
                    'fake_meter_number' => $newFakeMeterNumber,
                    'main_meter_number' => $mainEnergyMeter->meter_number,
                    'shared_household_id' => $validatedData['shared_user_household_id'] ?? null,
                    'shared_public_structure_id' => $validatedData['shared_user_public_structure_id'] ?? null,
                    'shared_household_name' => $sharedUserHousehold ? $sharedUserHousehold->english_name : 'Unknown'
                ]);

                return true;
            }

        } catch (\Exception $e) {
            \Log::error("Error processing shared energy meter", [
                'main_meter_number' => $validatedData['current_meter_number'] ?? 'unknown',
                'shared_household_id' => $validatedData['shared_user_household_id'] ?? null,
                'shared_public_structure_id' => $validatedData['shared_user_public_structure_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Create or update HouseholdMeter record to integrate with shared energy users system
     */
    private function createHouseholdMeterRecord($mainEnergyMeter, $sharedEnergyMeter, $validatedData)
    {
        try {
            // Check if household meter record already exists
            $existingHouseholdMeter = null;
            
            if (isset($validatedData['shared_user_household_id'])) {
                $existingHouseholdMeter = HouseholdMeter::where('household_id', $validatedData['shared_user_household_id'])
                    ->where('energy_user_id', $mainEnergyMeter->id)
                    ->where('is_archived', 0)
                    ->first();
            } elseif (isset($validatedData['shared_user_public_structure_id'])) {
                $existingHouseholdMeter = HouseholdMeter::where('public_structure_id', $validatedData['shared_user_public_structure_id'])
                    ->where('energy_user_id', $mainEnergyMeter->id)
                    ->where('is_archived', 0)
                    ->first();
            }

            if ($existingHouseholdMeter) {
                // Update existing household meter record
                \Log::info("Household meter record already exists", [
                    'household_meter_id' => $existingHouseholdMeter->id,
                    'shared_household_id' => $validatedData['shared_user_household_id'] ?? null,
                    'shared_public_structure_id' => $validatedData['shared_user_public_structure_id'] ?? null
                ]);
                return true;
            }

            // Create new household meter record
            $householdMeter = new HouseholdMeter();
            $householdMeter->energy_user_id = $mainEnergyMeter->id; // Links to main energy meter
            $householdMeter->is_archived = 0;

            // Get entity names for display
            $mainHolderName = 'Unknown Main Holder';
            $sharedUserName = 'Unknown Shared User';

            // Set main holder name
            if ($mainEnergyMeter->household_id) {
                $mainHousehold = Household::find($mainEnergyMeter->household_id);
                $mainHolderName = $mainHousehold ? $mainHousehold->english_name : 'Unknown Household';
            } elseif ($mainEnergyMeter->public_structure_id) {
                $mainPublicStructure = PublicStructure::find($mainEnergyMeter->public_structure_id);
                $mainHolderName = $mainPublicStructure ? $mainPublicStructure->english_name : 'Unknown Public Structure';
            }

            // Set shared user info and names
            if (isset($validatedData['shared_user_household_id'])) {
                $householdMeter->household_id = $validatedData['shared_user_household_id'];
                $sharedHousehold = Household::find($validatedData['shared_user_household_id']);
                if ($sharedHousehold) {
                    $sharedUserName = $sharedHousehold->english_name;
                    $householdMeter->user_name_arabic = $sharedHousehold->arabic_name;
                    $householdMeter->household_name = $sharedHousehold->english_name;
                }
            } elseif (isset($validatedData['shared_user_public_structure_id'])) {
                $householdMeter->public_structure_id = $validatedData['shared_user_public_structure_id'];
                $sharedPublicStructure = PublicStructure::find($validatedData['shared_user_public_structure_id']);
                if ($sharedPublicStructure) {
                    $sharedUserName = $sharedPublicStructure->english_name;
                    $householdMeter->household_name = $sharedPublicStructure->english_name;
                }
            }

            // Set the main user name (energy meter holder)
            $householdMeter->user_name = $mainHolderName;

            $householdMeter->save();

            \Log::info("HouseholdMeter record created successfully", [
                'household_meter_id' => $householdMeter->id,
                'main_energy_meter_id' => $mainEnergyMeter->id,
                'shared_energy_meter_id' => $sharedEnergyMeter->id,
                'main_holder_name' => $mainHolderName,
                'shared_user_name' => $sharedUserName,
                'shared_household_id' => $validatedData['shared_user_household_id'] ?? null,
                'shared_public_structure_id' => $validatedData['shared_user_public_structure_id'] ?? null
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error("Error creating HouseholdMeter record", [
                'main_meter_id' => $mainEnergyMeter->id ?? null,
                'shared_meter_id' => $sharedEnergyMeter->id ?? null,
                'shared_household_id' => $validatedData['shared_user_household_id'] ?? null,
                'shared_public_structure_id' => $validatedData['shared_user_public_structure_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Create/Update energy meter record when status is "Replaced"
     * Saves the new meter number to all_energy_meters table
     */
    private function createReplacedEnergyMeter($validatedData, $currentMeter)
    {
        try {
            // Find the current energy meter
            if (!$currentMeter) {
                \Log::error("Current energy meter not found for replaced meter creation", [
                    'meter_number' => $validatedData['current_meter_number'] ?? 'unknown'
                ]);
                return false;
            }

            // Check if another meter with the new meter number already exists (different from current meter)
            $existingMeter = AllEnergyMeter::where('meter_number', $validatedData['new_meter_number'])
                ->where('id', '!=', $currentMeter->id)
                ->where('is_archived', 0)
                ->first();

            if ($existingMeter) {
                // If another meter with new number already exists, log warning and skip update
                \Log::warning("Cannot update meter: Another meter with the new number already exists", [
                    'existing_meter_id' => $existingMeter->id,
                    'current_meter_id' => $currentMeter->id,
                    'new_meter_number' => $validatedData['new_meter_number'],
                    'old_meter_number' => $validatedData['current_meter_number'] ?? 'unknown'
                ]);
                return false;
            }

            // Update the existing meter record with the new meter number
            $oldMeterNumber = $currentMeter->meter_number;
            $currentMeter->meter_number = $validatedData['new_meter_number'];
            $currentMeter->save();

            \Log::info("Existing energy meter updated with new meter number", [
                'meter_id' => $currentMeter->id,
                'old_meter_number' => $oldMeterNumber,
                'new_meter_number' => $validatedData['new_meter_number'],
                'household_id' => $currentMeter->household_id,
                'public_structure_id' => $currentMeter->public_structure_id,
                'community_id' => $currentMeter->community_id
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error("Error creating replacement energy meter", [
                'old_meter_number' => $validatedData['current_meter_number'] ?? 'unknown',
                'new_meter_number' => $validatedData['new_meter_number'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}