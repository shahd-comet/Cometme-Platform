<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgricultureComponent;
use App\Models\AgricultureComponentCategory;

class AgricultureComponentController extends Controller
{
    public function index()
    {
        $items = AgricultureComponent::with('category')->orderBy('english_name')->paginate(25);
        return view('system.components.components.index', compact('items'));
    }

    public function create()
    {
        $categories = AgricultureComponentCategory::orderBy('name')->get();
        return view('system.components.components.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'english_name' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'agriculture_component_category_id' => 'required|exists:agriculture_component_categories,id',
            'is_archived' => 'sometimes|boolean'
        ]);
        AgricultureComponent::create($data);
        return redirect()->route('components.index')->with('success','Component created');
    }

    public function show($id)
    {
        $item = AgricultureComponent::with('category')->findOrFail($id);
        return view('system.components.components.show', compact('item'));
    }

    public function edit($id)
    {
        $item = AgricultureComponent::findOrFail($id);
        $categories = AgricultureComponentCategory::orderBy('name')->get();
        return view('system.components.components.edit', compact('item','categories'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'english_name' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'agriculture_component_category_id' => 'required|exists:agriculture_component_categories,id',
            'is_archived' => 'sometimes|boolean'
        ]);
        $item = AgricultureComponent::findOrFail($id);
        $item->update($data);
        return redirect()->route('components.index')->with('success','Component updated');
    }

    public function destroy($id)
    {
        $item = AgricultureComponent::findOrFail($id);
        $item->delete();
        return redirect()->route('components.index')->with('success','Component deleted');
    }
}
