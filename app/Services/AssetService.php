<?php

namespace App\Services;
namespace App\Services;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetService
{
    /**
     * Get all assets with optional filters
     */
    public function getAllAssets($filters = [])
    {
        $query = Asset::with(['currentEmployee']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('serial_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('brand', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('model', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('asset_code', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->get();
    }

    /**
     * Create a new asset
     */
    public function createAsset(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate unique asset code
            $data['asset_code'] = $this->generateAssetCode();
            $data['status'] = 'available';

            // Use formatted dates if available
            if (isset($data['purchase_date_formatted'])) {
                $data['purchase_date'] = $data['purchase_date_formatted'];
                unset($data['purchase_date_formatted']);
            }

            if (isset($data['warranty_expiry_formatted'])) {
                $data['warranty_expiry'] = $data['warranty_expiry_formatted'];
                unset($data['warranty_expiry_formatted']);
            }

            $asset = Asset::create($data);

            Log::info('Asset created', [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'name' => $asset->name
            ]);

            return $asset;
        });
    }

    /**
     * Update an existing asset
     */
    public function updateAsset(Asset $asset, array $data)
    {
        return DB::transaction(function () use ($asset, $data) {
            // Use formatted dates if available
            if (isset($data['purchase_date_formatted'])) {
                $data['purchase_date'] = $data['purchase_date_formatted'];
                unset($data['purchase_date_formatted']);
            }

            if (isset($data['warranty_expiry_formatted'])) {
                $data['warranty_expiry'] = $data['warranty_expiry_formatted'];
                unset($data['warranty_expiry_formatted']);
            }

            $asset->update($data);

            Log::info('Asset updated', [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code
            ]);

            return $asset->fresh();
        });
    }

    /**
     * Delete an asset
     */
    public function deleteAsset(Asset $asset)
    {
        return DB::transaction(function () use ($asset) {
            // Check if asset is currently assigned
            if ($asset->status === 'assigned') {
                throw new \Exception('Cannot delete assigned asset. Please unassign it first.');
            }

            // Check if asset has any active assignments
            $activeAssignments = $asset->employees()
                ->wherePivot('assignment_status', 'active')
                ->count();

            if ($activeAssignments > 0) {
                throw new \Exception('Cannot delete asset with active assignments.');
            }

            Log::info('Asset deleted', [
                'asset_id' => $asset->id,
                'asset_code' => $asset->asset_code
            ]);

            return $asset->delete();
        });
    }

    /**
     * Assign asset to an employee
     */
    public function assignAsset(Asset $asset, $employeeId, $assignmentNotes = null)
    {
        return DB::transaction(function () use ($asset, $employeeId, $assignmentNotes) {
            // Validate employee exists
            $employee = Employee::findOrFail($employeeId);

            // Check if asset is available
            if ($asset->status === 'assigned') {
                throw new \Exception('This asset is already assigned to someone else.');
            }

            if ($asset->status === 'maintenance') {
                throw new \Exception('Cannot assign asset that is under maintenance.');
            }

            if ($asset->status === 'retired') {
                throw new \Exception('Cannot assign retired asset.');
            }

            // Check for existing active assignment
            $existingAssignment = $asset->employees()
                ->wherePivot('assignment_status', 'active')
                ->exists();

            if ($existingAssignment) {
                throw new \Exception('Asset already has an active assignment.');
            }

            // Create assignment
            $asset->employees()->attach($employeeId, [
                'assigned_date' => date('n/j/Y'),
                'assignment_status' => 'active',
                'assignment_notes' => $assignmentNotes,
            ]);

            // Update asset status
            $asset->update(['status' => 'assigned']);

            Log::info('Asset assigned', [
                'asset_id' => $asset->id,
                'employee_id' => $employeeId,
                'asset_code' => $asset->asset_code
            ]);

            return $asset->fresh();
        });
    }

    /**
     * Unassign asset from employee
     */
    public function unassignAsset(Asset $asset, $returnNotes = null)
    {
        return DB::transaction(function () use ($asset, $returnNotes) {
            // Check if asset is assigned
            if ($asset->status !== 'assigned') {
                throw new \Exception('This asset is not currently assigned.');
            }

            // Get active assignment
            $activeAssignment = $asset->employees()
                ->wherePivot('assignment_status', 'active')
                ->first();

            if (!$activeAssignment) {
                throw new \Exception('No active assignment found for this asset.');
            }

            // Update assignment to returned
            $asset->employees()->updateExistingPivot($activeAssignment->id, [
                'return_date' => date('n/j/Y'),
                'assignment_status' => 'returned',
                'assignment_notes' => $returnNotes ?
                    $activeAssignment->pivot->assignment_notes . ' | Return: ' . $returnNotes :
                    $activeAssignment->pivot->assignment_notes,
            ]);

            // Update asset status to available
            $asset->update(['status' => 'available']);

            Log::info('Asset unassigned', [
                'asset_id' => $asset->id,
                'employee_id' => $activeAssignment->id,
                'asset_code' => $asset->asset_code
            ]);

            return $asset->fresh();
        });
    }

    /**
     * Update asset condition
     */
    public function updateCondition(Asset $asset, $condition)
    {
        $validConditions = ['excellent', 'good', 'fair', 'poor'];

        if (!in_array($condition, $validConditions)) {
            throw new \Exception('Invalid condition value.');
        }

        return DB::transaction(function () use ($asset, $condition) {
            $asset->update(['condition' => $condition]);

            Log::info('Asset condition updated', [
                'asset_id' => $asset->id,
                'old_condition' => $asset->getOriginal('condition'),
                'new_condition' => $condition
            ]);

            return $asset->fresh();
        });
    }

    /**
     * Update asset status
     */
    public function updateStatus(Asset $asset, $status)
    {
        $validStatuses = ['available', 'assigned', 'maintenance', 'retired'];

        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Invalid status value.');
        }

        return DB::transaction(function () use ($asset, $status) {
            // Prevent changing status to 'assigned' directly
            if ($status === 'assigned' && $asset->status !== 'assigned') {
                throw new \Exception('Use assign method to assign asset to employee.');
            }

            $asset->update(['status' => $status]);

            Log::info('Asset status updated', [
                'asset_id' => $asset->id,
                'old_status' => $asset->getOriginal('status'),
                'new_status' => $status
            ]);

            return $asset->fresh();
        });
    }

    /**
     * Get asset assignment history
     */
    public function getAssignmentHistory(Asset $asset)
    {
        return $asset->employees()
            ->withPivot('assigned_date', 'return_date', 'assignment_status', 'assignment_notes')
            ->orderBy('employee_asset.created_at', 'desc')
            ->get()
            ->map(function($employee) {
                return [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'department' => $employee->department,
                    'assigned_date' => $employee->pivot->assigned_date,
                    'return_date' => $employee->pivot->return_date,
                    'status' => $employee->pivot->assignment_status,
                    'notes' => $employee->pivot->assignment_notes,
                ];
            });
    }

    /**
     * Generate unique asset code
     */
    private function generateAssetCode()
    {
        $lastAsset = Asset::withTrashed()->latest('id')->first();
        $number = $lastAsset ? intval(substr($lastAsset->asset_code, 3)) + 1 : 1;
        return 'AST' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get assets statistics
     */
    public function getStatistics()
    {
        return [
            'total' => Asset::count(),
            'available' => Asset::available()->count(),
            'assigned' => Asset::assigned()->count(),
            'maintenance' => Asset::maintenance()->count(),
            'retired' => Asset::retired()->count(),
            'total_value' => Asset::sum('value'),
            'by_type' => Asset::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
            'by_condition' => Asset::select('condition', DB::raw('count(*) as count'))
                ->groupBy('condition')
                ->get(),
            'warranty_expiring' => Asset::warrantyExpiring(30)->count(),
        ];
    }
}
