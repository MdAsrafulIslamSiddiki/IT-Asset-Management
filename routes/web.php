<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LicenseController;
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

Route::get('/report', [ReportController::class, 'index'])->name('report');
