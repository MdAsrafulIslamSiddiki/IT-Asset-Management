<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Services\EmployeeService;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        return view('employees.index', compact('employees'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            $employee = $this->employeeService->createEmployee($request->validated());

            // For AJAX requests
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee created successfully',
                    'employee' => $employee
                ]);
            }

            // For form submissions
            return redirect()->route('employees.index')->with('success', 'Employee created successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create employee'
                ], 500);
            }
            return back()->with('error', 'Failed to create employee');
        }
    }

    public function show(Employee $employee)
    {
        $employee->load(['assets', 'licenses']);

        return response()->json([
            'id' => $employee->id,
            'name' => $employee->name,
            'iqama_id' => $employee->iqama_id,
            'email' => $employee->email,
            'department' => $employee->department,
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
                'name' => $l->name,
                'expiry_date' => $l->pivot->expiry_date ?? 'N/A',
            ])
        ]);
    }

    public function edit(Employee $employee)
    {
        return response()->json([
            'name' => $employee->name,
            'iqama_id' => $employee->iqama_id,
            'email' => $employee->email,
            'department' => $employee->department,
            'position' => $employee->position,
            'join_date' => $employee->join_date
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $updatedEmployee = $this->employeeService->updateEmployee($employee, $request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee updated successfully',
                    'employee' => $updatedEmployee
                ]);
            }

            return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update employee'
                ], 500);
            }
            return back()->with('error', 'Failed to update employee');
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $this->employeeService->deleteEmployee($employee);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee deleted successfully'
                ]);
            }

            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
