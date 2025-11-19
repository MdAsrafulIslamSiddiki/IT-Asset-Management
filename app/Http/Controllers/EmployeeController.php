<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Asset;
use App\Models\License;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of employees
     */
    public function index()
    {
        try {
            $employees = $this->employeeService->getAllEmployees();

            // Get available assets and licenses for assignment modals
            $availableAssets = Asset::where('status', 'available')->get();
            $availableLicenses = License::all();

            return view('employees.index', compact('employees', 'availableAssets', 'availableLicenses'));
        } catch (\Exception $e) {
            Log::error('Employee index error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load employees');
        }
    }

    /**
     * Store a newly created employee
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $employee = $this->employeeService->createEmployee($request->validated());

            Log::info('Employee created', [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'created_by' => auth()->id() ?? 'system'
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee created successfully',
                    'employee' => $employee
                ]);
            }

            return redirect()->route('employees.index')
                ->with('success', 'Employee created successfully');
        } catch (\Exception $e) {
            Log::error('Employee creation failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create employee: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'Failed to create employee');
        }
    }

    /**
     * Display the specified employee
     */
    public function show(Employee $employee)
    {
        try {
            $employee->load(['assets', 'licenses']);

            return response()->json([
                'id' => $employee->id,
                'name' => $employee->name,
                'iqama_id' => $employee->iqama_id,
                'email' => $employee->email,
                'department' => $employee->department,
                'position' => $employee->position,
                'join_date' => $employee->join_date,
                'status' => $employee->status,
                'assets' => $employee->assets->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'serial_number' => $a->serial_number,
                    'condition' => $a->condition ?? 'good',
                ]),
                'licenses' => $employee->licenses->map(fn($l) => [
                    'id' => $l->id,
                    'name' => $l->name ?? 'Unknown License',
                    'expiry_date' => $l->pivot->expiry_date ?? 'N/A',
                ])
            ]);
        } catch (\Exception $e) {
            Log::error('Employee show error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load employee details'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit(Employee $employee)
    {
        try {
            return response()->json([
                'name' => $employee->name,
                'iqama_id' => $employee->iqama_id,
                'email' => $employee->email,
                'department' => $employee->department,
                'position' => $employee->position,
                'join_date' => $employee->join_date
            ]);
        } catch (\Exception $e) {
            Log::error('Employee edit error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load employee data'
            ], 404);
        }
    }

    /**
     * Update the specified employee
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $updatedEmployee = $this->employeeService->updateEmployee($employee, $request->validated());

            Log::info('Employee updated', [
                'employee_id' => $employee->id,
                'updated_by' => auth()->id() ?? 'system'
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee updated successfully',
                    'employee' => $updatedEmployee
                ]);
            }

            return redirect()->route('employees.index')
                ->with('success', 'Employee updated successfully');
        } catch (\Exception $e) {
            Log::error('Employee update failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update employee'
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'Failed to update employee');
        }
    }

    /**
     * Remove the specified employee
     */
    public function destroy(Employee $employee)
    {
        try {
            $this->employeeService->deleteEmployee($employee);

            Log::info('Employee deleted', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'deleted_by' => auth()->id() ?? 'system'
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee deleted successfully'
                ]);
            }

            return redirect()->route('employees.index')
                ->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            Log::error('Employee deletion failed: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign an asset to employee
     */
    public function assignAsset(Request $request, Employee $employee)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $asset = Asset::findOrFail($request->asset_id);

            // Check if asset is already assigned (using pivot table)
            $existingAssignment = $employee->assets()
                ->where('asset_id', $asset->id)
                ->wherePivot('assignment_status', 'active')
                ->exists();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This asset is already assigned to this employee'
                ], 400);
            }

            // Check if asset is assigned to ANY employee (using pivot table)
            $isAssetAssigned = \DB::table('employee_asset')
                ->where('asset_id', $asset->id)
                ->where('assignment_status', 'active')
                ->exists();

            if ($isAssetAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'This asset is already assigned to another employee'
                ], 400);
            }

            // Remove this direct assignment (NO LONGER NEEDED)
            // $asset->employee_id = $employee->id;
            // $asset->status = 'assigned';
            // $asset->save();

            // Update asset status only
            $asset->status = 'assigned';
            $asset->save();

            // Create assignment record in pivot table (ONLY THIS)
            $employee->assets()->attach($asset->id, [
                'assigned_date' => date('Y-m-d'), // Use Y-m-d format for database
                'assignment_notes' => $request->notes,
                'assignment_status' => 'active'
            ]);

            Log::info('Asset assigned to employee via pivot', [
                'employee_id' => $employee->id,
                'asset_id' => $asset->id,
                'assigned_by' => auth()->id() ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Asset assigned successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Asset assignment failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a license to employee
     */
    public function assignLicense(Request $request, Employee $employee)
    {
        $request->validate([
            'license_id' => 'required|exists:licenses,id',
            'expiry_date' => 'required|string'
        ]);

        try {
            // Check if already assigned
            $existing = $employee->licenses()->where('license_id', $request->license_id)->exists();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'This license is already assigned to this employee'
                ], 400);
            }

            // Get the license
            $license = License::findOrFail($request->license_id);

            // Check license availability
            if ($license->used_quantity >= $license->total_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available license seats remaining'
                ], 400);
            }

            // Assign license to employee
            $employee->licenses()->attach($request->license_id, [
                'assigned_date' => date('Y-m-d'),
                'expiry_date' => date('Y-m-d', strtotime($request->expiry_date)),
                'status' => 'active'
            ]);

            // Update used_quantity - INCREMENT by 1
            $license->increment('used_quantity');

            Log::info('License assigned to employee', [
                'employee_id' => $employee->id,
                'license_id' => $request->license_id,
                'assigned_by' => auth()->id() ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'License assigned successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('License assignment failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign license: ' . $e->getMessage()
            ], 500);
        }
    }
}
