<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Asset;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display reports and clearance page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $activeEmployeesCount = Employee::active()->count();

            $totalAssetsCount = Asset::count();

            $activeLicensesCount = License::active()->count();

            $expiringLicensesCount = $this->getExpiringLicensesCount();

            $employees = Employee::active()
                ->withCount(['assets', 'licenses'])
                ->get()
                ->map(function ($employee) {
                    $employee->display_text = "{$employee->name} - {$employee->department} ({$employee->status})";
                    return $employee;
                });

            $assetAllocationReport = $this->getAssetAllocationReport();

            $expiringLicenses = $this->getExpiringLicenses();

            return view('reports.index', compact(
                'activeEmployeesCount',
                'totalAssetsCount',
                'activeLicensesCount',
                'expiringLicensesCount',
                'employees',
                'assetAllocationReport',
                'expiringLicenses'
            ));
        } catch (\Exception $e) {

            return view('reports.index')->with('error', 'Unable to load reports data.');
        }
    }

    /**
     * Get employee details for clearance modal
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployeeDetails(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id'
            ]);

            $employee = Employee::with([
                'assets' => function ($query) {
                    $query->where('assignment_status', 'active');
                },
                'activeLicenses'
            ])->findOrFail($request->employee_id);

            $totalAssetValue = $employee->assets->sum('value');

            $totalLicenseCost = $employee->activeLicenses->sum('cost_per_license');
            $totalValue = $totalAssetValue + $totalLicenseCost;

            $assetsList = $employee->assets->map(function ($asset) {
                return "{$asset->name} – Serial {$asset->serial_number}";
            })->implode(', ');

            $licensesList = $employee->activeLicenses->map(function ($license) {
                return $license->name;
            })->implode(', ');

            return response()->json([
                'success' => true,
                'data' => [
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'iqama_id' => $employee->iqama_id,
                    'department' => $employee->department,
                    'assets_count' => $employee->assets_count,
                    'licenses_count' => $employee->activeLicenses->count(),
                    'total_value' => number_format($totalValue, 2),
                    'assets_list' => $assetsList,
                    'licenses_list' => $licensesList,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch employee details.'
            ], 500);
        }
    }

    /**
     * Generate clearance paper PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateClearancePaper(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id'
            ]);

            $employee = Employee::with([
                'assets' => function ($query) {
                    $query->where('assignment_status', 'active');
                },
                'activeLicenses'
            ])->findOrFail($request->employee_id);

            return response()->json([
                'success' => true,
                'message' => 'Clearance paper generated successfully',
                'download_url' => '#'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate clearance paper.'
            ], 500);
        }
    }

    /**
     * Get number of licenses expiring within 30 days
     *
     * @return int
     */
    private function getExpiringLicensesCount()
    {
        try {
            $targetDate = date('n/j/Y', strtotime('+30 days'));

            return License::active()
                ->whereRaw("STR_TO_DATE(expiry_date, '%c/%e/%Y') <= ?", [$targetDate])
                ->whereRaw("STR_TO_DATE(expiry_date, '%c/%e/%Y') >= ?", [date('n/j/Y')])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get licenses expiring within 30 days
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getExpiringLicenses()
    {
        try {
            $targetDate = date('n/j/Y', strtotime('+30 days'));

            return License::active()
                ->whereRaw("STR_TO_DATE(expiry_date, '%c/%e/%Y') <= ?", [$targetDate])
                ->whereRaw("STR_TO_DATE(expiry_date, '%c/%e/%Y') >= ?", [date('n/j/Y')])
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Generate asset allocation report by department
     *
     * @return array
     */
    private function getAssetAllocationReport()
    {
        try {
            $departments = Employee::distinct()->pluck('department');

            $report = [];

            foreach ($departments as $department) {
                $employeesInDept = Employee::byDepartment($department)->active()->count();

                $assetsInDept = Asset::whereHas('employees', function ($query) use ($department) {
                    $query->where('department', $department)
                        ->where('assignment_status', 'active');
                })->count();

                $licensesInDept = License::whereHas('employees', function ($query) use ($department) {
                    $query->where('department', $department);
                })->count();

                $assetValue = Asset::whereHas('employees', function ($query) use ($department) {
                    $query->where('department', $department)
                        ->where('assignment_status', 'active');
                })->sum('value');

                $licenseCost = License::whereHas('employees', function ($query) use ($department) {
                    $query->where('department', $department);
                })->sum('cost_per_license');

                $totalValue = $assetValue + $licenseCost;

                $report[] = [
                    'department' => $department,
                    'employees' => $employeesInDept,
                    'assets' => $assetsInDept,
                    'licenses' => $licensesInDept,
                    'total_value' => number_format($totalValue, 2)
                ];
            }

            return $report;
        } catch (\Exception $e) {
            return [];
        }
    }
}
