<?php

namespace App\Http\Controllers;

use App\Models\Datactual;
use Illuminate\Http\Request;

class DatactualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datactual = Datactual::all();
        return view('pages.admin.variabel.index', compact('datactual'));
    }

    public function data()
    {
        $datactual = Datactual::all();
        return datatables()
            ->of($datactual)
            ->addIndexColumn()
            ->addColumn('action', function ($datactual) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModalActual(' . $datactual->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModalActual(' . $datactual->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['action'])
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
        ]);
        $data = $request->all();
        $datactual = Datactual::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area created successfully'
            ]);
        }

        return redirect()->route('datactual.index')->with('success_message_create', 'Variabel created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Datactual $datactual)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $datactual = Datactual::findOrFail($id);
        return response()->json($datactual);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $datactual = Datactual::findOrFail($id);
        $data = $request->all();

        $datactual->update($data);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Variabel updated successfully'
            ]);
        }

        return redirect()->route('datactual.index')->with('success_message_update', 'Actual updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $datactual = Datactual::findOrFail($id);
        $datactual->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully'
            ]);
        }

        return redirect()->route('datactual.index')->with('success_message_delete', 'Area deleted successfully.');
    }
}
