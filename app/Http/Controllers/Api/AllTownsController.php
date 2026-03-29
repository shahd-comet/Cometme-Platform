<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Town;
use App\Exports\TownExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class AllTownsController extends Controller
{
    /**
     * Display a listing of all towns.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $towns = Town::with('region')
                         ->where('is_archived', false)
                         ->orderBy('id', 'asc')
                         ->get()
                         ->map(function ($town) {
                             return [
                                 'english_name' => $town->english_name,
                                 'arabic_name' => $town->arabic_name,
                                 'region' => $town->region_name ? $town->region->english_name : null,
                                 'comet-ID'=> $town->comet_id
                             ];
                         });

            return response()->json([
                'success' => true,
                'message' => 'Towns retrieved successfully',
                'data' => $towns,
                'count' => $towns->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve towns',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified town.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $town = Town::with('region')->find($id);

            if (! $town) {
                return response()->json([
                    'success' => false,
                    'message' => 'Town not found'
                ], 404);
            }

            $data = [
                'id' => $town->id,
                'english_name' => $town->english_name,
                'arabic_name' => $town->arabic_name,
                'region' => $town->region ? [
                    'id' => $town->region->id,
                    'english_name' => $town->region->english_name,
                ] : null,
                'comet_id' => $town->comet_id,
                'is_archived' => (bool) $town->is_archived,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Town retrieved successfully',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve town',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Export towns to an Excel file
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        try {
            return Excel::download(new TownExport(), 'towns.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting towns',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}