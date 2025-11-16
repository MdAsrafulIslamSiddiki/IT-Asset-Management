<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Ahmed Al-Rahman',
                'iqama_id' => '2234567890',
                'email' => 'ahmed.rahman@company.com',
                'department' => 'IT',
                'position' => 'Senior Developer',
                'join_date' => '1/15/2023',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Johnson',
                'iqama_id' => '2234567891',
                'email' => 'sarah.johnson@company.com',
                'department' => 'Marketing',
                'position' => 'Marketing Manager',
                'join_date' => '8/20/2022',
                'status' => 'active',
            ],
            [
                'name' => 'Mohammed Ali',
                'iqama_id' => '2234567892',
                'email' => 'mohammed.ali@company.com',
                'department' => 'Finance',
                'position' => 'Financial Analyst',
                'join_date' => '5/10/2023',
                'status' => 'active',
            ],
            [
                'name' => 'Lisa Chen',
                'iqama_id' => '2234567893',
                'email' => 'lisa.chen@company.com',
                'department' => 'HR',
                'position' => 'HR Specialist',
                'join_date' => '3/1/2023',
                'status' => 'inactive',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
