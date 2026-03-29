<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ImportMeterHistory;
use Maatwebsite\Excel\Facades\Excel;

class MeterHistoryImportController extends Controller
{
    public function index()
    {
        return view('meter-history.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        $file = $request->file('excel_file');

        try {
            // Reset counters before import
            ImportMeterHistory::resetCounters();

            // Import the Excel file
            Excel::import(new ImportMeterHistory, $file);

            // Get import statistics
            $stats = ImportMeterHistory::stats();
            
            // Log final summary
            \Log::info("IMPORT SUMMARY: Total processed rows, Inserted: {$stats['inserted']}, Skipped: {$stats['skipped']}");
            \Log::info("Please check the log above for details on why specific rows were skipped.");

            // Build detailed success message
            $message = "Import completed successfully! ";
            $message .= "✅ Inserted: {$stats['inserted']} new records. ";
            
            if ($stats['skipped'] > 0) {
                $message .= "⏭️ Skipped: {$stats['skipped']} duplicate/invalid records (already exist in database). ";
            } else {
                $message .= "🎉 No duplicates found - all records were new! ";
            }

            // Redirect to meter history table to see the imported records
            return redirect()->route('meter-history.all')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Meter History Import Error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage() . '. Please check your Excel file format and try again.');
        }
    }
}