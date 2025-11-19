<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $activeEmployees = Employee::where('status', 'active')->count();

        $assignedAssets = DB::table('employee_asset')
            ->where('assignment_status', 'active')
            ->count();

        $totalLicenses = License::count();

        $expiringSoon = License::where('expiry_date', '<=', now()->addDays(30))
                                ->where('status', 'active')
                                ->count();

        $recentAssets = DB::table('employee_asset')
            ->where('assignment_status', 'active')
            ->orderBy('assigned_date', 'desc')
            ->join('assets', 'employee_asset.asset_id', '=', 'assets.id')
            ->join('employees', 'employee_asset.employee_id', '=', 'employees.id')
            ->select('assets.name as asset_name', 'employees.name as employee_name')
            ->take(5)
            ->get();

        $licenses = License::all();

        return view('dashboard', compact(
            'activeEmployees',
            'assignedAssets',
            'totalLicenses',
            'expiringSoon',
            'recentAssets',
            'licenses'
        ));
    }
}
