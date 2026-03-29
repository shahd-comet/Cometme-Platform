<?php

namespace App\Http\Controllers;
use App\Models\Town;
use App\Models\Region;
use Illuminate\Http\Request;

class TownController extends Controller
{
    // Show the list of towns
    public function index()
    {

        $towns = Town::with('region')->get();
        $regions = Region::orderBy('english_name')->get();
        return view('regions.towns.index', compact('towns', 'regions'));
    }
    public function update(Request $request, int $id)
    {
        // Validate the request data   
        $request->validate([
            'english_name' => 'required|string|max:255',
            'arabic_name' => 'nullable|string|max:255',
            'region_id' => 'required|integer|exists:regions,id',
        ]);

        try {
            $town = Town::findOrFail($id);
            
            // Update the town with validated data
            $town->update([
                'english_name' => $request->english_name,
                'arabic_name' => $request->arabic_name,
                'region_id' => $request->region_id,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Town updated successfully.',
                'town' => $town
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update town: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'english_name' => 'required|string|max:255',
            'arabic_name' => 'nullable|string|max:255',
            'region_id' => 'required|integer|exists:regions,id',
        ]);

        // Get Last comet_id
        $last_comet_id = Town::latest('id')->value('comet_id');
        
        try {
            $town = Town::create([
                'english_name' => $request->english_name,
                'arabic_name' => $request->arabic_name,
                'region_id' => $request->region_id,
                'comet_id' => ++$last_comet_id
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Town created successfully.',
                'town' => $town
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to create town: ' . $e->getMessage()
            ], 500);
        }
    }
 
    public function deleteTown(Request $request)
    {
        $townId = $request->query('id');
        $town = Town::find($townId);

        if ($town) {
            $town->delete();
            return response()->json(['success' => true, 'message' => 'Town deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Town not found.'], 404);
        }
    }
}