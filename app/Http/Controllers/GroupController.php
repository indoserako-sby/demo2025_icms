<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return view('pages.admin.group.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('group', 'public');
        } else {
            $validated['image'] = null;
        }
        $group = Group::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
            ]);
        }
        return redirect()->route('group.index')->with('success', 'Group created successfully');
    }

    public function data()
    {
        $groups = Group::with('area')->get();
        return datatables()
            ->of($groups)
            ->addIndexColumn()
            ->addColumn('area', function ($group) {
                return $group->area->name;
            })
            ->addColumn('image', function ($group) {
                return '<img src="' . Storage::url($group->image) . '" alt="Area Image" width="50" height="50">';
            })
            ->addColumn('action', function ($group) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal(' . $group->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(' . $group->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['action', 'area', 'image'])
            ->make(true);
    }
    public function edit(string $id)
    {
        $group = Group::findOrFail($id);
        return response()->json($group);
    }

    public function show(Group $group)
    {
        return response()->json($group->load('area'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $group = Group::findOrFail($id);
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('group', 'public');
        } else {
            $data['image'] = $group->image;
        }
        $group->update($data);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Group updated successfully',
            ]);
        }
        return redirect()->route('group.index')->with('success', 'Group updated successfully');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json(null, 204);
    }
}
