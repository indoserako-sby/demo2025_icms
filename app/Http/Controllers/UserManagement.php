<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagement extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.admin.user-management.index');
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);
        $validated['password'] = bcrypt($request->input('password'));
        User::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
            ]);
        }
        return redirect()->route('user-management.index')->with('success', 'User created successfully');
    }

    public function data()
    {
        $users = User::all();
        return datatables()
            ->of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                return '
                    <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal(' . $user->id . ')">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(' . $user->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        if ($request->input('password')) {
            $validated['password'] = bcrypt($request->input('password'));
        }
        $validated['role'] = $request->input('role');
        $user = User::findOrFail($id);
        $user->update($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
            ]);
        }
        return redirect()->route('user-management.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)


    {
        try {
            $user = User::findOrFail($id);
            // Force delete if using soft deletes
            $user->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully',
                ]);
            }
            return redirect()->route('user-management.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            // Log the error

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete user: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->route('user-management.index')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
