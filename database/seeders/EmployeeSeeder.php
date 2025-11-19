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
                'iqama_id' => '2314567890',
                'email' => 'ahmed.rahman@techsolutions.sa',
                'department' => 'Software Development',
                'position' => 'Senior Full Stack Developer',
                'join_date' => '2023-01-15',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Johnson',
                'iqama_id' => '2314567891',
                'email' => 'sarah.johnson@techsolutions.sa',
                'department' => 'Digital Marketing',
                'position' => 'Marketing Director',
                'join_date' => '2022-08-20',
                'status' => 'active',
            ],
            [
                'name' => 'Mohammed Ali',
                'iqama_id' => '2314567892',
                'email' => 'mohammed.ali@techsolutions.sa',
                'department' => 'Finance & Accounting',
                'position' => 'Senior Financial Analyst',
                'join_date' => '2023-05-10',
                'status' => 'active',
            ],
            [
                'name' => 'Lisa Chen',
                'iqama_id' => '2314567893',
                'email' => 'lisa.chen@techsolutions.sa',
                'department' => 'Human Resources',
                'position' => 'HR Business Partner',
                'join_date' => '2023-03-01',
                'status' => 'active',
            ],
            [
                'name' => 'Omar Abdullah',
                'iqama_id' => '2314567894',
                'email' => 'omar.abdullah@techsolutions.sa',
                'department' => 'IT Support',
                'position' => 'IT Support Specialist',
                'join_date' => '2024-01-08',
                'status' => 'active',
            ],
            [
                'name' => 'Fatima Zahra',
                'iqama_id' => '2314567895',
                'email' => 'fatima.zahra@techsolutions.sa',
                'department' => 'Software Development',
                'position' => 'Frontend Developer',
                'join_date' => '2023-09-15',
                'status' => 'active',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
