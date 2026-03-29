<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use DB; 
use Route;
use App\Models\CameraComponentAccessory;
use DataTables;

class CameraComponentAccessoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
        if (Auth::guard('user')->user() != null) {

            if ($request->ajax()) {
                
                $data = DB::table('camera_components')
                    ->select('camera_components.id', 'camera_components.component_name', 'camera_components.component_type', 'camera_components.description')
                    ->latest();   
 
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) {
                        $updateButton = "<a type='button' class='updateComponentAccessory' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
                        $deleteButton = "<a type='button' class='deleteComponentAccessory' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
                        
                        if(Auth::guard('user')->user()->user_type_id != 1 || 
                            Auth::guard('user')->user()->user_type_id != 6 || 
                            Auth::guard('user')->user()->user_type_id != 10) 
                        {
                                
                            return $updateButton." ".$deleteButton;
                        } 

                    })
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                                $instance->where(function($w) use($request) {
                                    $search = $request->get('search');
                                    $w->orWhere('camera_components.component_name', 'LIKE', "%$search%")
                                    ->orWhere('camera_components.component_type', 'LIKE', "%$search%")
                                    ->orWhere('camera_components.description', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('services.camera.components.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'component_name' => 'required|string|max:255',
            'component_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        CameraComponentAccessory::create($validated);
        return redirect()->route('camera-component-accessory.index')->with('component_accessory_message', 'Camera component & accessory created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $componentAccessory = CameraComponentAccessory::findOrFail($id);
        return view('services.camera.components.component_accessory.edit', compact('componentAccessory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'component_name' => 'required|string|max:255',
            'component_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $componentAccessory = CameraComponentAccessory::findOrFail($id);
        $componentAccessory->update($validated);
        return redirect()->route('camera-component-accessory.index')->with('component_accessory_message', 'Camera component & accessory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Show edit page
     */
    public function editPage($id)
    {
        $componentAccessory = CameraComponentAccessory::findOrFail($id);
        return response()->json($componentAccessory);
    }

    /**
     * Delete component accessory
     */
    public function deleteComponentAccessory(Request $request)
    {
        $id = $request->id;
        $componentAccessory = CameraComponentAccessory::find($id);
        
        if($componentAccessory) {
            $componentAccessory->delete();
            return response()->json(['success' => 1, 'msg' => 'Camera component & accessory deleted successfully.']);
        } else {
            return response()->json(['success' => 0, 'msg' => 'Camera component & accessory not found.']);
        }
    }
} 