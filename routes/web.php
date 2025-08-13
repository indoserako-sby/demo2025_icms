<?php

use App\Http\Controllers\AlertAsset;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DatactualController;
use App\Http\Controllers\DatvarController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MachineParameterController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\ParameterSensorController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ListDataController;
use App\Http\Controllers\UserManagement;
use App\Livewire\NestedDataTable;
use App\Livewire\CrossAssetAnalysis;
use App\Livewire\SelectionTreePanel;
use App\Models\ParameterSensor;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    } else {
        return redirect()->route('login');
    }
});

// Data routes for datatables
Route::get('area/data', [AreaController::class, 'data'])->name('area.data');
Route::get('group/data', [GroupController::class, 'data'])->name('group.data');
Route::get('asset/data', [AssetController::class, 'data'])->name('asset.data');
Route::get('machine-parameter/data', [MachineParameterController::class, 'data'])->name('machine-parameter.data');
Route::get('position/data', [PositionController::class, 'data'])->name('position.data');
Route::get('datvar/data', [DatvarController::class, 'data'])->name('datvar.data');
Route::get('datactual/data', [DatactualController::class, 'data'])->name('datactual.data');
Route::get('list-data/data', [ListDataController::class, 'data'])->name('list-data.data');
Route::get('user-management/data', [UserManagement::class, 'data'])->name('user-management.data');
Route::get('displayvibration', function () {
    return view('pages.display.vvb3');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard accessible to all authenticated users
    Route::get('/dashboard', function () {
        return view('pages.user.dashboard.index');
    })->name('user.dashboard');

    Route::middleware(['role:user'])->group(function () {
        // User-specific routes can be placed here
    });
    Route::get('/asset-analysis', function () {
        return view('pages.user.asset-analysis.index');
    })->name('user.asset-analysis');

    // Cross Asset Analysis Route
    Route::get('/cross-asset-analysis', function () {
        return view('pages.user.cross-asset-analysis.index');
    })->name('user.cross-asset-analysis');
    Route::get('/asset-information', function () {
        return view('pages.user.asset-information.index');
    })->name('user.asset-information');

    // Add a direct route to update chart parameters without relying on Livewire events
    Route::post('/update-chart-parameters', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'asset_id' => 'required',
            'parameters' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'interval' => 'nullable|string',
        ]);

        $assetId = $request->input('asset_id');
        $parameters = $request->input('parameters', []);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $interval = $request->input('interval', 'raw'); // Default to raw data if not specified

        // Return the data that would be used for the chart
        $chart = new \App\Livewire\AssetAnalysisChart();
        $chart->mount($assetId);

        // Set date range - sekarang wajib
        $chart->startDate = $startDate;
        $chart->endDate = $endDate;

        // Set interval if provided
        if ($interval) {
            $chart->interval = $interval;
        }

        if (!empty($parameters)) {
            $chart->updateSelectedParameters($parameters);
        }

        // Load the chart data
        if (method_exists($chart, 'loadChartData')) {
            $chart->loadChartData();
        }

        return response()->json([
            'success' => true,
            'chartData' => $chart->chartData ?? null,
            'interval' => $interval
        ]);
    })->name('update-chart-parameters');

    // Export log data for asset parameters
    Route::post('/export-log-data', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'asset_id' => 'required|integer',
            'parameters' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'interval' => 'nullable|string',
        ]);

        $assetId = $request->input('asset_id');
        $parameters = $request->input('parameters');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $interval = $request->input('interval', 'raw');

        // Get asset name for filename
        $asset = \App\Models\Asset::find($assetId);
        $assetName = $asset ? str_replace(' ', '_', $asset->name) : 'asset';

        // Add interval to filename if not raw
        $intervalText = '';
        if ($interval !== 'raw') {
            $intervalText = '_' . $interval;
        }

        // Generate filename with asset name, date range and interval
        $fileName = 'log_data_' . $assetName . '_' . $startDate . '_to_' . $endDate . $intervalText . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LogDataExport(
                $assetId,
                $parameters,
                $startDate,
                $endDate,
                $interval
            ),
            $fileName
        );
    })->name('export-log-data');

    // Cross Asset Analysis Chart Update Route
    Route::post('/update-cross-chart-parameters', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'parameters' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'interval' => 'nullable|string',
        ]);

        $parameters = $request->input('parameters', []);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $interval = $request->input('interval', 'raw'); // Default to raw data if not specified

        try {
            // Initialize the chart component
            $chart = new \App\Livewire\CrossAssetAnalysis();

            // Mount and initialize with default data
            $chart->mount();

            // Set the parameters, dates and interval
            $chart->selectedParameters = $parameters;
            $chart->startDate = $startDate;
            $chart->endDate = $endDate;
            $chart->interval = $interval;

            // Force readyToLoad to true since we have parameters
            $chart->readyToLoad = true;

            // Debug log before generating chart


            // Generate chart data
            $chart->generateChart();


            if (empty($chart->chartData)) {
                throw new \Exception('Chart data is empty after generation');
            }

            return response()->json([
                'success' => true,
                'chartData' => $chart->chartData,
                'interval' => $interval,
                'debug' => [
                    'parameters' => $parameters,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'debug' => [
                    'parameters' => $parameters,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]
            ], 500);
        }
    })->name('update-cross-chart-parameters');

    // Cross Asset Log Data Export Route
    Route::post('/export-cross-asset-log-data', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'parameters' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'interval' => 'nullable|string',
        ]);

        $parameters = $request->input('parameters');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $interval = $request->input('interval', 'raw'); // Default to raw data if not specified

        // Add interval to filename if not raw
        $intervalText = '';
        if ($interval !== 'raw') {
            $intervalText = '_' . $interval;
        }

        // Generate filename with date range and interval
        $fileName = 'cross_asset_log_data_' . $startDate . '_to_' . $endDate . $intervalText . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CrossAssetLogDataExport(
                $parameters,
                $startDate,
                $endDate,
                $interval
            ),
            $fileName
        );
    })->name('export-cross-asset-log-data');

    Route::get('alert-asset', function () {
        return view('pages.user.Alert.index');
    })->name('alert-asset');

    Route::get('historical-alert', function () {
        return view('pages.user.Alert.historical');
    })->name('historical-alert');


    // Admin Routes with /admin prefix
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('pages.admin.dashboard.index');
        })->name('admin.dashboard');
        Route::resource('area', AreaController::class);
        Route::resource('group', GroupController::class);
        Route::resource('asset', AssetController::class);
        Route::resource('machine-parameter', MachineParameterController::class);
        Route::resource('position', PositionController::class);
        Route::resource('datvar', DatvarController::class);
        Route::resource('datactual', DatactualController::class);
        Route::resource('list-data', ListDataController::class);
        Route::resource('user-management', UserManagement::class);

        // Data monitoring using nested data table
        Route::get('/monitoring', function () {
            return view('pages.admin.monitoring.index');
        })->name('admin.monitoring');

        // Example route for the SelectionTreePanel demo
        Route::get('/examples/selection-tree-panel', function () {
            return view('examples.selection-tree-panel-demo');
        })->name('examples.selection-tree-panel');

        // Chart Display with Selection Tree Panel



        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';
