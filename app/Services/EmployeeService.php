<?php
namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function getAllEmployees()
    {
        // return Employee::withCount(['assets', 'licenses'])->latest()->get();
        return Employee::withCount(['assets'])->latest()->get();
    }

    public function createEmployee(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = 'active';
            return Employee::create($data);
        });
    }

    public function updateEmployee(Employee $employee, array $data)
    {
        return DB::transaction(function () use ($employee, $data) {
            $employee->update($data);
            return $employee->fresh();
        });
    }

    public function deleteEmployee(Employee $employee)
    {
        return DB::transaction(function () use ($employee) {
            if ($employee->assets()->count() > 0) {
                throw new \Exception('Cannot delete employee with assigned assets');
            }

            $employee->licenses()->detach();
            return $employee->delete();
        });
    }

    public function getEmployeeWithDetails($id)
    {
        return Employee::with(['assets', 'licenses'])->findOrFail($id);
    }

}
