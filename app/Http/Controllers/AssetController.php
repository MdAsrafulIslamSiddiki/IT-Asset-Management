<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Services\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'type', 'condition', 'search']);
        $assets = $this->assetService->getAllAssets($filters);
        $employees = Employee::active()->get();
        $statistics = $this->assetService->getStatistics();

        return view('assets.index', compact('assets', 'employees', 'statistics'));
    }

    public function store(StoreAssetRequest $request)
    {
        try {
            $asset = $this->assetService->createAsset($request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset created successfully',
                    'asset' => $asset
                ]);
            }

            return redirect()->route('asset-management.index')->with('success', 'Asset created successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create asset: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'Failed to create asset: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $asset = Asset::findOrFail($id);
            $asset->load(['currentEmployee', 'employees']);
            $history = $this->assetService->getAssignmentHistory($asset);

            $currentEmployee = $asset->currentEmployee->first();

            return response()->json([
                'id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'type' => $asset->type,
                'serial_number' => $asset->serial_number,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'purchase_date' => $asset->purchase_date,
                'warranty_expiry' => $asset->warranty_expiry,
                'value' => number_format($asset->value, 2),
                'depreciated_value' => number_format($asset->depreciated_value, 2),
                'condition' => $asset->condition,
                'status' => $asset->status,
                'notes' => $asset->notes,
                'is_warranty_expired' => $asset->is_warranty_expired,
                'current_employee' => $currentEmployee ? [
                    'id' => $currentEmployee->id,
                    'name' => $currentEmployee->name,
                    'department' => $currentEmployee->department,
                    'assigned_date' => $currentEmployee->pivot->assigned_date,
                    'assignment_notes' => $currentEmployee->pivot->assignment_notes,
                ] : null,
                'assignment_history' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found: ' . $e->getMessage()
            ], 404);
        }
    }

    public function edit($id)
    {
        try {
            $asset = Asset::findOrFail($id);

            return response()->json([
                'name' => $asset->name,
                'type' => $asset->type,
                'serial_number' => $asset->serial_number,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'purchase_date' => $asset->purchase_date,
                'warranty_expiry' => $asset->warranty_expiry,
                'value' => $asset->value,
                'condition' => $asset->condition,
                'notes' => $asset->notes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found'
            ], 404);
        }
    }

    public function update(UpdateAssetRequest $request, $id)
    {
        try {
            $asset = Asset::findOrFail($id);
            $updatedAsset = $this->assetService->updateAsset($asset, $request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset updated successfully',
                    'asset' => $updatedAsset
                ]);
            }

            return redirect()->route('asset-management.index')->with('success', 'Asset updated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $asset = Asset::findOrFail($id);
            $this->assetService->deleteAsset($asset);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset deleted successfully'
                ]);
            }

            return redirect()->route('asset-management.index')->with('success', 'Asset deleted successfully');
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

    public function assign(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assignment_notes' => 'nullable|string|max:500',
        ]);

        try {
            $asset = Asset::findOrFail($id);
            $this->assetService->assignAsset(
                $asset,
                $request->employee_id,
                $request->assignment_notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Asset assigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function unassign(Request $request, $id)
    {
        $request->validate([
            'return_notes' => 'nullable|string|max:500',
        ]);

        try {
            $asset = Asset::findOrFail($id);
            $this->assetService->unassignAsset($asset, $request->return_notes);

            return response()->json([
                'success' => true,
                'message' => 'Asset unassigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }



    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,maintenance,retired',
        ]);

        try {
            $asset = Asset::findOrFail($id);
            $this->assetService->updateStatus($asset, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Asset status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
