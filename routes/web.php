<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

// Employee Management Routes
Route::resource('employees', EmployeeController::class);
// Additional Employee Routes
Route::post('employees/{employee}/assign-asset', [EmployeeController::class, 'assignAsset'])
    ->name('employees.assign-asset');
Route::post('employees/{employee}/assign-license', [EmployeeController::class, 'assignLicense'])
    ->name('employees.assign-license');
Route::get('employees/{employee}/clearance', [EmployeeController::class, 'generateClearance'])
    ->name('employees.clearance');


// assets routes
Route::resource('asset-management', AssetController::class);
Route::post('asset-management/{asset}/assign', [AssetController::class, 'assign'])
    ->name('asset-management.assign');
Route::post('asset-management/{asset}/unassign', [AssetController::class, 'unassign'])
    ->name('asset-management.unassign');
Route::post('asset-management/{asset}/status', [AssetController::class, 'updateStatus'])
    ->name('asset-management.status');



// license routes
Route::resource('licenses', LicenseController::class);
Route::post('licenses/{license}/assign', [LicenseController::class, 'assign'])
    ->name('licenses.assign');
Route::post('licenses/{license}/revoke', [LicenseController::class, 'revoke'])
    ->name('licenses.revoke');


// search route
Route::get('/search', [SearchController::class, 'index'])->name('search.index');


// Route::get('/report', [ReportController::class, 'index'])->name('report.index');
// Route::post('/employees/{id}/clearance', [EmployeeController::class, 'downloadClearance'])
//         ->name('employees.clearance.download');


Route::prefix('reports')->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/employee-details', [ReportsController::class, 'getEmployeeDetails'])->name('reports.employee-details');
    Route::post('/generate-clearance', [ReportsController::class, 'generateClearancePaper'])->name('reports.generate-clearance');
});
