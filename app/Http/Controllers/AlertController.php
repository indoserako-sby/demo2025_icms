<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::with(['sensor.asset', 'parameter'])
            ->latest('triggered_at')
            ->paginate(50);
        return response()->json($alerts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sensor_id' => 'required|exists:sensors,id',
            'parameter_id' => 'required|exists:parameters,id',
            'type' => 'required|in:warning,danger',
            'value' => 'required|numeric',
            'message' => 'required|string',
            'status' => 'required|in:active,acknowledged,resolved',
            'triggered_at' => 'required|date',
            'acknowledged_at' => 'nullable|date',
            'resolved_at' => 'nullable|date'
        ]);

        $alert = Alert::create($validated);
        return response()->json($alert, 201);
    }

    public function show(Alert $alert)
    {
        return response()->json($alert->load(['sensor.asset', 'parameter']));
    }

    public function update(Request $request, Alert $alert)
    {
        $validated = $request->validate([
            'sensor_id' => 'required|exists:sensors,id',
            'parameter_id' => 'required|exists:parameters,id',
            'type' => 'required|in:warning,danger',
            'value' => 'required|numeric',
            'message' => 'required|string',
            'status' => 'required|in:active,acknowledged,resolved',
            'triggered_at' => 'required|date',
            'acknowledged_at' => 'nullable|date',
            'resolved_at' => 'nullable|date'
        ]);

        $alert->update($validated);
        return response()->json($alert);
    }

    public function destroy(Alert $alert)
    {
        $alert->delete();
        return response()->json(null, 204);
    }
}
