<?php

namespace App\Http\Controllers;

use App\Models\MeterHistoryStatuses;
use App\Models\MeterHistoryReason;
use Illuminate\Http\Request;

class MeterHistoryComponentController extends Controller
{
    public function index()
    {
        $statuses = MeterHistoryStatuses::all(); 
        $reasons = MeterHistoryReason::all();
        return view('meter-history-component.index', compact('statuses', 'reasons'));
    }

    // Status Methods
    public function storeStatus(Request $request)
    {
        $request->validate([
            'english_name' => 'required|string|max:255',
            'arabic_name' => 'string|max:255',
        ]);

        MeterHistoryStatuses::create($request->all());
        return redirect()->back()->with('success', 'Status created successfully');
    }

    public function updateStatus(Request $request, MeterHistoryStatuses $status)
    {
        $request->validate([
            'english_name' => 'required|string|max:255',
            'arabic_name' => 'string|max:255',
        ]);

        $status->update($request->all());
        return redirect()->back()->with('success', 'Status updated successfully');
    }

    public function deleteStatus(MeterHistoryStatuses $status)
    {
        $status->delete();
        return redirect()->back()->with('success', 'Status deleted successfully');
    }

    // Reason Methods
    public function storeReason(Request $request)
    {
        $request->validate([
            'english_name' => 'required|string|max:255',
            'arabic_name' => 'string|max:255',]);

        MeterHistoryReason::create($request->all());
        return redirect()->back()->with('success', 'Reason created successfully');
    }

    public function updateReason(Request $request, MeterHistoryReason $reason)
    {
        $request->validate([
            'english_name' => 'required|string|max:255',
            'arabic_name' => 'string|max:255',
        ]);

        $reason->update($request->all());
        return redirect()->back()->with('success', 'Reason updated successfully');
    }

    public function deleteReason(MeterHistoryReason $reason)
    {
        $reason->delete();
        return redirect()->back()->with('success', 'Reason deleted successfully');
    }
}