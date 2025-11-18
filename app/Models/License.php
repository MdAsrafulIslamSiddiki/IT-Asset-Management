<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'license_code',
        'name',
        'vendor',
        'license_key',
        'license_type',
        'total_quantity',
        'used_quantity',
        'purchase_date',
        'expiry_date',
        'cost_per_license',
        'status',
        'notes',
    ];

    // Relationships
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_license')
            ->withPivot('assigned_date', 'expiry_date', 'status')
            ->withTimestamps();
    }

    // Accessors
    public function getAvailableQuantityAttribute()
    {
        return $this->total_quantity - $this->used_quantity;
    }

    public function getTotalCostAttribute()
    {
        return $this->total_quantity * $this->cost_per_license;
    }

    public function getIsExpiredAttribute()
    {
        // Simple string comparison for mm/dd/yyyy format
        return strtotime($this->expiry_date) < time();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeAvailable($query)
    {
        return $query->whereColumn('used_quantity', '<', 'total_quantity');
    }
}
