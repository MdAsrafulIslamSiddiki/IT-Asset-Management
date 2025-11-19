<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');
        $employee = null;
        $assets = collect();
        $licenses = collect();
        $statistics = [
            'total_assets' => 0,
            'total_licenses' => 0,
            'asset_value' => 0,
            'license_cost' => 0,
        ];

        if ($searchQuery) {
            $employee = Employee::where('iqama_id', 'LIKE', "%{$searchQuery}%")
                ->orWhere('email', 'LIKE', "%{$searchQuery}%")
                ->first();

            if ($employee) {
                $assets = DB::table('employee_asset')
                    ->join('assets', 'employee_asset.asset_id', '=', 'assets.id')
                    ->where('employee_asset.employee_id', $employee->id)
                    ->where('employee_asset.assignment_status', 'active')
                    ->select(
                        'assets.id',
                        'assets.name',
                        'assets.serial_number',
                        'assets.value',
                        'assets.condition',
                        'employee_asset.assigned_date'
                    )
                    ->get();

                $licenses = DB::table('employee_license')
                    ->join('licenses', 'employee_license.license_id', '=', 'licenses.id')
                    ->where('employee_license.employee_id', $employee->id)
                    ->where('employee_license.status', 'active')
                    ->select(
                        'licenses.id',
                        'licenses.name',
                        'licenses.expiry_date',
                        'licenses.cost_per_license',
                        'employee_license.status',
                        'employee_license.assigned_date'
                    )
                    ->get();

                $assetValue = $assets->sum('value');
                $licenseCost = $licenses->sum('cost_per_license') * 12;

                $statistics = [
                    'total_assets' => $assets->count(),
                    'total_licenses' => $licenses->count(),
                    'asset_value' => $assetValue,
                    'license_cost' => $licenseCost,
                ];
            }
        }

        return view('search.index', compact('searchQuery', 'employee', 'assets', 'licenses', 'statistics'));
    }
}
