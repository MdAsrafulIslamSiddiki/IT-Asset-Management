<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\License;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        $assets = [
            [
                'asset_code' => 'AST2024001',
                'name' => 'MacBook Pro 16" M3 Max',
                'type' => 'Laptop',
                'serial_number' => 'C02XYZ123ABC',
                'brand' => 'Apple',
                'model' => 'MacBook Pro M3 Max 2024',
                'purchase_date' => '2024-01-15',
                'warranty_expiry' => '2027-01-15',
                'value' => 3899.00,
                'condition' => 'excellent',
                'status' => 'assigned',
            ],
            [
                'asset_code' => 'AST2024002',
                'name' => 'Dell XPS 15 9530',
                'type' => 'Laptop',
                'serial_number' => '7XYZ123ABCDE456',
                'brand' => 'Dell',
                'model' => 'XPS 15 9530',
                'purchase_date' => '2024-02-20',
                'warranty_expiry' => '2027-02-20',
                'value' => 2249.99,
                'condition' => 'excellent',
                'status' => 'assigned',
            ],
            [
                'asset_code' => 'AST2024003',
                'name' => 'iPhone 15 Pro Max',
                'type' => 'Phone',
                'serial_number' => 'LN123456789ABCD',
                'brand' => 'Apple',
                'model' => 'iPhone 15 Pro Max 256GB',
                'purchase_date' => '2024-03-10',
                'warranty_expiry' => '2025-03-10',
                'value' => 1399.00,
                'condition' => 'excellent',
                'status' => 'assigned',
            ],
            [
                'asset_code' => 'AST2024004',
                'name' => 'Samsung Galaxy S24 Ultra',
                'type' => 'Phone',
                'serial_number' => 'R38ZXYZ123456789',
                'brand' => 'Samsung',
                'model' => 'Galaxy S24 Ultra 512GB',
                'purchase_date' => '2024-04-05',
                'warranty_expiry' => '2026-04-05',
                'value' => 1499.99,
                'condition' => 'good',
                'status' => 'available',
            ],
            [
                'asset_code' => 'AST2024005',
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'type' => 'Laptop',
                'serial_number' => 'PF5XYZ12ABC',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad X1 Carbon Gen 11',
                'purchase_date' => '2024-01-30',
                'warranty_expiry' => '2027-01-30',
                'value' => 1899.00,
                'condition' => 'good',
                'status' => 'maintenance',
            ],
            [
                'asset_code' => 'AST2024006',
                'name' => 'iPad Pro 12.9" M2',
                'type' => 'Tablet',
                'serial_number' => 'DLXWXYZ12345ABC',
                'brand' => 'Apple',
                'model' => 'iPad Pro 12.9" 6th Gen',
                'purchase_date' => '2024-03-25',
                'warranty_expiry' => '2026-03-25',
                'value' => 1299.00,
                'condition' => 'excellent',
                'status' => 'available',
            ],
            [
                'asset_code' => 'AST2023050',
                'name' => 'HP EliteBook 840 G9',
                'type' => 'Laptop',
                'serial_number' => '5CD123XYZABC',
                'brand' => 'HP',
                'model' => 'EliteBook 840 G9',
                'purchase_date' => '2023-11-15',
                'warranty_expiry' => '2026-11-15',
                'value' => 1650.00,
                'condition' => 'fair',
                'status' => 'assigned',
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}
