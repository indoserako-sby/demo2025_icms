<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function index()
    {
        $groups = Group::with('area')->get();
        return view('pages.admin.asset.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:assets,code',
            'description' => 'nullable|string',
            'status' => 'nullable|in:good,warning,danger'
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('asset', 'public');
        } else {
            $validated['image'] = null;
        }
        $asset = Asset::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset created successfully',
            ]);
        }
    }
    public function data()
    {
        $assets = Asset::with('group')->get();
        return datatables()
            ->of($assets)
            ->addIndexColumn()
            ->addColumn('group', function ($assets) {
                return $assets->group->name;
            })
            ->addColumn('image', function ($assets) {
                return '<img src="' . Storage::url($assets->image) . '" alt="Area Image" width="50" height="50">';
            })
            ->addColumn('action', function ($assets) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal(' . $assets->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(' . $assets->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['action', 'group', 'image'])
            ->make(true);
    }

    public function show(Asset $asset)
    {
        return response()->json($asset->load(['group.area', 'sensors']));
    }

    public function edit(string $id)
    {
        $asset = Asset::findOrFail($id);
        return response()->json($asset);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:assets,code,' . $id,
            'description' => 'nullable|string',
            'status' => 'nullable|in:good,warning,danger'
        ]);
        $asset = Asset::findOrFail($id);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('asset', 'public');
        } else {
            $validated['image'] = $asset->image;
        }

        $asset->update($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset updated successfully',
            ]);
        }
        return redirect()->route('asset.index')->with('success', 'Asset updated successfully');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(null, 204);
    }
}
