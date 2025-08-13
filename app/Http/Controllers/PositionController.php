<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::all();
        return view('pages.admin.machine-parameter.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function data()
    {
        $positions = Position::all();
        return datatables()
            ->of($positions)
            ->addIndexColumn()
            ->addColumn('action', function ($position) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModalPosition(' . $position->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModalPosition(' . $position->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $position = Position::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Position created successfully'
            ]);
        }

        return redirect()->route('position.index')->with('success_message_create', 'Position created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $position = Position::findOrFail($id);
        return response()->json($position);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $position->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Position updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        $position->delete();
        return response()->json([
            'success' => true,
            'message' => 'Position deleted successfully'
        ]);
    }
}
