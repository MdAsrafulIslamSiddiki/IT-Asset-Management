<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeService
{
    /**
     * Get all employees with counts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllEmployees()
    {
        try {
            return Employee::withCount(['assets', 'licenses'])
                ->latest()
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to fetch employees: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new employee
     *
     * @param array $data
     * @return Employee
     * @throws \Exception
     */
    public function createEmployee(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Set default status
                $data['status'] = $data['status'] ?? 'active';

                // Create employee
                $employee = Employee::create($data);

                Log::info('Employee created successfully', [
                    'employee_id' => $employee->id,
                    'name' => $employee->name
                ]);

                return $employee;

            } catch (\Exception $e) {
                Log::error('Employee creation failed in service: ' . $e->getMessage(), [
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing employee
     *
     * @param Employee $employee
     * @param array $data
     * @return Employee
     * @throws \Exception
     */
    public function updateEmployee(Employee $employee, array $data)
    {
        return DB::transaction(function () use ($employee, $data) {
            try {
                $employee->update($data);

                Log::info('Employee updated successfully', [
                    'employee_id' => $employee->id,
                    'name' => $employee->name
                ]);

                return $employee->fresh();

            } catch (\Exception $e) {
                Log::error('Employee update failed in service: ' . $e->getMessage(), [
                    'employee_id' => $employee->id,
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Delete an employee
     *
     * @param Employee $employee
     * @return bool
     * @throws \Exception
     */
    public function deleteEmployee(Employee $employee)
    {
        return DB::transaction(function () use ($employee) {
            try {
                // Check for assigned assets
                $assignedAssetsCount = $employee->assets()
                    ->where('status', 'assigned')
                    ->count();

                if ($assignedAssetsCount > 0) {
                    throw new \Exception(
                        "Cannot delete employee with {$assignedAssetsCount} assigned asset(s). " .
                        "Please unassign all assets first."
                    );
                }

                // Detach all licenses
                $employee->licenses()->detach();

                // Delete employee
                $result = $employee->delete();

                Log::info('Employee deleted successfully', [
                    'employee_id' => $employee->id,
                    'name' => $employee->name
                ]);

                return $result;

            } catch (\Exception $e) {
                Log::error('Employee deletion failed in service: ' . $e->getMessage(), [
                    'employee_id' => $employee->id
                ]);
                throw $e;
            }
        });
    }

    /**
     * Get employee with full details
     *
     * @param int $id
     * @return Employee
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getEmployeeWithDetails($id)
    {
        try {
            return Employee::with(['assets', 'licenses'])
                ->withCount(['assets', 'licenses'])
                ->findOrFail($id);

        } catch (\Exception $e) {
            Log::error('Failed to fetch employee details: ' . $e->getMessage(), [
                'employee_id' => $id
            ]);
            throw $e;
        }
    }

    /**
     * Get active employees only
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveEmployees()
    {
        return Employee::active()
            ->withCount(['assets', 'licenses'])
            ->get();
    }

    /**
     * Deactivate an employee
     *
     * @param Employee $employee
     * @return Employee
     */
    public function deactivateEmployee(Employee $employee)
    {
        return DB::transaction(function () use ($employee) {
            $employee->update(['status' => 'inactive']);

            Log::info('Employee deactivated', [
                'employee_id' => $employee->id,
                'name' => $employee->name
            ]);

            return $employee->fresh();
        });
    }

    /**
     * Reactivate an employee
     *
     * @param Employee $employee
     * @return Employee
     */
    public function reactivateEmployee(Employee $employee)
    {
        return DB::transaction(function () use ($employee) {
            $employee->update(['status' => 'active']);

            Log::info('Employee reactivated', [
                'employee_id' => $employee->id,
                'name' => $employee->name
            ]);

            return $employee->fresh();
        });
    }
}
