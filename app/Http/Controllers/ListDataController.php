<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Datvar;
use App\Models\ListData;
use App\Models\MachineParameter;
use App\Models\Position;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ListDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::all();
        $machineParameters = MachineParameter::all();
        $positions = Position::all();
        $datvars = Datvar::all();
        return view('pages.admin.list-data.index', compact(
            'assets',
            'machineParameters',
            'positions',
            'datvars'
        ));
    }

    /**
     * Process datatables ajax request.
     */
    public function data()
    {
        $query = ListData::query()
            ->join('areas', 'list_data.area_id', '=', 'areas.id')
            ->join('groups', 'list_data.group_id', '=', 'groups.id')
            ->join('assets', 'list_data.asset_id', '=', 'assets.id')
            ->join('machine_parameters', 'list_data.machine_parameter_id', '=', 'machine_parameters.id')
            ->join('positions', 'list_data.position_id', '=', 'positions.id')
            ->join('datvars', 'list_data.datvar_id', '=', 'datvars.id')
            ->select([
                'list_data.*',
                'areas.name as area_name',
                'groups.name as group_name',
                'assets.name as asset_name',
                'machine_parameters.name as machine_parameter_name',
                'positions.name as position_name',
                'datvars.name as datvar_name',
            ]);

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('area', function ($listData) {
                return $listData->area_name;
            })
            ->addColumn('group', function ($listData) {
                return $listData->group_name;
            })
            ->addColumn('asset', function ($listData) {
                return $listData->asset_name;
            })
            ->addColumn('machine_parameter', function ($listData) {
                return $listData->machine_parameter_name;
            })
            ->addColumn('position', function ($listData) {
                return $listData->position_name;
            })
            ->addColumn('datvar', function ($listData) {
                return $listData->datvar_name;
            })
            ->addColumn('action', function ($listData) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal(' . $listData->id . ')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(' . $listData->id . ')"><i class="fas fa-trash"></i></button>
                ';
            })
            // Menambahkan filter custom untuk kolom relasi
            ->filterColumn('area', function ($query, $keyword) {
                $query->where('areas.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('group', function ($query, $keyword) {
                $query->where('groups.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('asset', function ($query, $keyword) {
                $query->where('assets.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('machine_parameter', function ($query, $keyword) {
                $query->where('machine_parameters.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('position', function ($query, $keyword) {
                $query->where('positions.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('datvar', function ($query, $keyword) {
                $query->where('datvars.name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::all();
        $machineParameters = MachineParameter::all();
        $positions = Position::all();
        $datvars = Datvar::all();

        return view('pages.admin.list-data.create', compact(
            'assets',
            'machineParameters',
            'positions',
            'datvars'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'machine_parameter_id' => 'required|exists:machine_parameters,id',
            'position_id' => 'required|exists:positions,id',
            'datvar_id' => 'required|exists:datvars,id',
            'warning_limit' => 'nullable|numeric',
            'danger_limit' => 'nullable|numeric',
        ]);

        // Get the asset to retrieve area_id and group_id
        $asset = Asset::findOrFail($request->asset_id);

        // Add area_id and group_id to the data
        $validated['group_id'] = $asset->group_id;

        $validated['area_id'] = $asset->group->area_id;

        // Create the list data
        ListData::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
            ]);
        }

        return redirect()->route('list-data.index')
            ->with('success', 'List data created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ListData $listData)
    {
        return view('pages.admin.list-data.show', compact('listData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $listData = ListData::findOrFail($id)->load(['area', 'group', 'asset', 'machineParameter', 'position', 'datvar']);
        return response()->json($listData);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'machine_parameter_id' => 'required|exists:machine_parameters,id',
            'position_id' => 'required|exists:positions,id',
            'datvar_id' => 'required|exists:datvars,id',
            'value' => 'nullable|numeric',
            'warning_limit' => 'nullable|numeric',
            'danger_limit' => 'nullable|numeric',
        ]);

        // Get the asset to retrieve area_id and group_id
        $listData = ListData::findOrFail($id);
        $asset = Asset::findOrFail($validated['asset_id']);
        // Check if the asset_id has changed
        if ($listData->asset_id != $validated['asset_id']) {
            // If it has changed, update the group_id and area_id
            $validated['group_id'] = $asset->group_id;
            $validated['area_id'] = $asset->group->area_id;
        } else {
            // If it hasn't changed, keep the existing group_id and area_id
            $validated['group_id'] = $listData->group_id;
            $validated['area_id'] = $listData->area_id;
        }
        // Update the list data
        $listData->update($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'List data updated successfully',
            ]);
        }

        return redirect()->route('list-data.index')
            ->with('success', 'List data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ListData $listData)
    {
        $listData->delete();

        return redirect()->route('list-data.index')
            ->with('success', 'List data deleted successfully.');
    }
}
