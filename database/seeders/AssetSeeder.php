<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        $assets = [
            [
                'asset_code' => 'AST00001',
                'name' => 'MacBook Pro 16"',
                'type' => 'Laptop',
                'serial_number' => 'MBP2023001',
                'brand' => 'Apple',
                'model' => 'MacBook Pro M2',
                'purchase_date' => '1/10/2023',
                'warranty_expiry' => '1/10/2026',
                'value' => 2500.00,
                'condition' => 'excellent',
                'status' => 'available',
            ],
            [
                'asset_code' => 'AST00002',
                'name' => 'Dell XPS 13',
                'type' => 'Laptop',
                'serial_number' => 'DXP2023002',
                'brand' => 'Dell',
                'model' => 'XPS 13 9310',
                'purchase_date' => '2/15/2023',
                'warranty_expiry' => '2/15/2026',
                'value' => 1800.00,
                'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'AST00003',
                'name' => 'iPhone 14 Pro',
                'type' => 'Phone',
                'serial_number' => 'IP14P001',
                'brand' => 'Apple',
                'model' => 'iPhone 14 Pro',
                'purchase_date' => '3/1/2023',
                'warranty_expiry' => '3/1/2025',
                'value' => 1200.00,
                'condition' => 'excellent',
                'status' => 'available',
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}
