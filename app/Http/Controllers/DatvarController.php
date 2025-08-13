<?php

namespace App\Http\Controllers;

use App\Models\Datvar;
use Illuminate\Http\Request;

class DatvarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $datvar = Datvar::all();
        return view('pages.admin.variabel.index', compact('datvar'));
    }

    public function data()
    {
        $datvar = Datvar::all();
        return datatables()
            ->of($datvar)
            ->addIndexColumn()
            ->addColumn('action', function ($area) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal(' . $area->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(' . $area->id . ')">Delete</button>
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
            'unit' => 'nullable|string|max:50',
        ]);
        $data = $request->all();
        $datvar = Datvar::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area created successfully'
            ]);
        }

        return redirect()->route('datvar.index')->with('success_message_create', 'Variabel created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Datvar $datvar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $datvar = Datvar::findOrFail($id);
        return response()->json($datvar);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
        ]);
        $datvar = Datvar::findOrFail($id);
        $data = $request->all();

        $datvar->update($data);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Variabel updated successfully'
            ]);
        }

        return redirect()->route('datvar.index')->with('success_message_update', 'Variabel updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $datvar = Datvar::findOrFail($id);
        $datvar->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully'
            ]);
        }

        return redirect()->route('datvar.index')->with('success_message_delete', 'Area deleted successfully.');
    }
}
