<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::all();
        return view('pages.admin.area.index', compact('areas'));
    }

    public function data()
    {
        $areas = Area::all();
        return datatables()
            ->of($areas)
            ->addIndexColumn()
            ->addColumn('image', function ($area) {
                return '<img src="' . Storage::url($area->image) . '" alt="Area Image" width="50" height="50">';
            })
            ->addColumn('action', function ($area) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal(' . $area->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(' . $area->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['action', 'image'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('areas', 'public');
        }

        $area = Area::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area created successfully'
            ]);
        }

        return redirect()->route('area.index')->with('success_message_create', 'Area created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $area = Area::findOrFail($id);
        return response()->json($area);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $area = Area::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($area->image) {
                Storage::disk('public')->delete($area->image);
            }
            // Store new image
            $data['image'] = $request->file('image')->store('areas', 'public');
        } else {
            // Keep old image
            $data['image'] = $area->image;
        }

        $area->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area updated successfully'
            ]);
        }

        return redirect()->route('area.index')->with('success_message_update', 'Area updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $area = Area::findOrFail($id);

        // Delete image if exists
        if ($area->image) {
            Storage::disk('public')->delete($area->image);
        }

        $area->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully'
            ]);
        }

        return redirect()->route('area.index')->with('success_message_delete', 'Area deleted successfully.');
    }
}
