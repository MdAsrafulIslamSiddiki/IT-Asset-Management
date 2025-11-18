<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'type',
        'serial_number',
        'brand',
        'model',
        'purchase_date',
        'warranty_expiry',
        'value',
        'condition',
        'status',
        'notes',
    ];

    // Relationships
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_asset')
            ->withPivot('assigned_date', 'return_date', 'assignment_status', 'assignment_notes')
            ->withTimestamps();
    }

    public function currentEmployee()
    {
        return $this->belongsToMany(Employee::class, 'employee_asset')
            ->wherePivot('assignment_status', 'active')
            ->withPivot('assigned_date', 'assignment_notes')
            ->withTimestamps()
            ->limit(1);
    }

    // Accessors
    public function getIsAssignedAttribute()
    {
        return $this->status === 'assigned';
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'available';
    }

    public function getIsWarrantyExpiredAttribute()
    {
        return strtotime($this->warranty_expiry) < time();
    }

    public function getDepreciatedValueAttribute()
    {
        // Simple depreciation: 20% per year
        $purchaseDate = strtotime($this->purchase_date);
        $yearsPassed = (time() - $purchaseDate) / (365 * 24 * 60 * 60);
        $depreciation = $this->value * 0.20 * $yearsPassed;
        return max(0, $this->value - $depreciation);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeRetired($query)
    {
        return $query->where('status', 'retired');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeWarrantyExpiring($query, $days = 30)
    {
        $targetDate = date('n/j/Y', strtotime("+$days days"));
        return $query->whereRaw("STR_TO_DATE(warranty_expiry, '%c/%e/%Y') <= ?", [$targetDate]);
    }
}
