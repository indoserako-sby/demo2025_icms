<?php

namespace App\Http\Controllers;

use App\Models\MachineParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MachineParameterController extends Controller
{
    public function index()
    {
        $machineParameters = MachineParameter::all();
        return view('pages.admin.machine-parameter.index', compact('machineParameters'));
    }

    public function data()
    {
        $machineParameters = MachineParameter::all();
        return datatables()
            ->of($machineParameters)
            ->addIndexColumn()
            ->addColumn('action', function ($machineParameter) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal(' . $machineParameter->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(' . $machineParameter->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $machineParameter = MachineParameter::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Machine Parameter created successfully'
            ]);
        }

        return redirect()->route('machine-parameter.index')->with('success_message_create', 'Machine Parameter created successfully.');
    }

    public function update(Request $request, MachineParameter $machineParameter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $machineParameter->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Machine Parameter updated successfully'
            ]);
        }

        return redirect()->route('machine-parameter.index')->with('success_message_update', 'Machine Parameter updated successfully.');
    }

    public function destroy(MachineParameter $machineParameter)
    {
        $machineParameter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Machine Parameter deleted successfully'
        ]);
    }
    public function edit(string $id)
    {
        $parameters = MachineParameter::findOrFail($id);
        return response()->json($parameters);
    }
}
