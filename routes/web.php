<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

// Route::get('/employee', [EmployeeController::class,'index'])->name('employee.index');

Route::resource('employees', EmployeeController::class);

// Additional routes for asset and license assignment
Route::post('employees/{employee}/assign-asset', [EmployeeController::class, 'assignAsset'])
    ->name('employees.assign-asset');
Route::post('employees/{employee}/assign-license', [EmployeeController::class, 'assignLicense'])
    ->name('employees.assign-license');


Route::resource('asset-management', AssetController::class);
Route::post('asset-management/{asset}/assign', [AssetController::class, 'assign'])->name('asset-management.assign');
Route::post('asset-management/{asset}/unassign', [AssetController::class, 'unassign'])->name('asset-management.unassign');

Route::get('/license', [LicenseController::class, 'index'])->name('license');
Route::get('/report', [ReportController::class, 'index'])->name('report');
