<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function getAllAssets($filters = [])
    {
        $query = Asset::with('employee');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('serial_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('brand', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->get();
    }

    public function createAsset(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['asset_code'] = $this->generateAssetCode();
            $data['status'] = 'available';
            return Asset::create($data);
        });
    }

    public function updateAsset(Asset $asset, array $data)
    {
        return DB::transaction(function () use ($asset, $data) {
            $asset->update($data);
            return $asset->fresh();
        });
    }

    public function deleteAsset(Asset $asset)
    {
        return DB::transaction(function () use ($asset) {
            if ($asset->status === 'assigned') {
                throw new \Exception('Cannot delete assigned asset. Please unassign first.');
            }
            return $asset->delete();
        });
    }

    public function assignAsset(Asset $asset, $employeeId)
    {
        return DB::transaction(function () use ($asset, $employeeId) {
            if ($asset->status === 'assigned') {
                throw new \Exception('Asset is already assigned');
            }

            $asset->update([
                'employee_id' => $employeeId,
                'status' => 'assigned',
            ]);

            return $asset->fresh();
        });
    }

    public function unassignAsset(Asset $asset)
    {
        return DB::transaction(function () use ($asset) {
            $asset->update([
                'employee_id' => null,
                'status' => 'available',
            ]);

            return $asset->fresh();
        });
    }

    private function generateAssetCode()
    {
        $lastAsset = Asset::latest('id')->first();
        $number = $lastAsset ? intval(substr($lastAsset->asset_code, 3)) + 1 : 1;
        return 'AST' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getAssetTypes()
    {
        return Asset::distinct()->pluck('type');
    }
}
