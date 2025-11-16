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
            $this->employeeService->createEmployee($request->validated());
            return response()->json(['success' => true, 'message' => 'Employee created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create employee'], 500);
        }
    }

    // public function show(Employee $employee)
    // {
    //     $employee->load(['assets', 'licenses']);

    //     return response()->json([
    //         'id' => $employee->id,
    //         'name' => $employee->name,
    //         'iqama_id' => $employee->iqama_id,
    //         'email' => $employee->email,
    //         'department' => $employee->department,
    //         'join_date' => $employee->join_date,
    //         'status' => $employee->status,
    //         'assets' => $employee->assets->map(fn($a) => [
    //             'id' => $a->id,
    //             'name' => $a->name,
    //             'serial_number' => $a->serial_number,
    //             'condition' => $a->condition ?? 'good',
    //         ]),
    //         'licenses' => $employee->licenses->map(fn($l) => [
    //             'id' => $l->id,
    //             'name' => $l->name,
    //             'expiry_date' => $l->pivot->expiry_date ?? 'N/A',
    //         ])
    //     ]);
    // }

    public function show(Employee $employee)
{
    return response()->json([
        'id' => $employee->id,
        'name' => $employee->name,
        'iqama_id' => $employee->iqama_id,
        'email' => $employee->email,
        'department' => $employee->department,
        'position' => $employee->position, // Eita add koro
        'join_date' => $employee->join_date,
        'status' => $employee->status,
        'assets' => [], // Empty array diye dao
        'licenses' => [] // Empty array diye dao
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
            $this->employeeService->updateEmployee($employee, $request->validated());
            return response()->json(['success' => true, 'message' => 'Employee updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update employee'], 500);
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $this->employeeService->deleteEmployee($employee);
            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
