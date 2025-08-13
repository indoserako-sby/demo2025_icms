<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\ParameterSensorController;
use App\Http\Controllers\LogDataController;
use App\Http\Controllers\AlertController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Asset Monitoring System Routes
Route::apiResource('areas', AreaController::class);
Route::apiResource('groups', GroupController::class);
Route::apiResource('assets', AssetController::class);
Route::apiResource('sensors', SensorController::class);
Route::apiResource('parameters', ParameterController::class);
Route::apiResource('parameter-sensors', ParameterSensorController::class);
Route::apiResource('log-data', LogDataController::class);
Route::apiResource('alerts', AlertController::class);

// Additional Parameter-Sensor routes
Route::post('sensors/{sensor}/parameters', [ParameterSensorController::class, 'attachParameter']);
Route::delete('sensors/{sensor}/parameters/{parameter}', [ParameterSensorController::class, 'detachParameter']);
