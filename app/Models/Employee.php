<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Employee extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'iqama_id',
        'email',
        'department',
        'position',
        'join_date',
        'status',
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'employee_asset')
            ->withPivot('assigned_date', 'return_date', 'assignment_status', 'assignment_notes')
            ->withTimestamps()
            ->wherePivot('assignment_status', 'active');
    }


    public function allAssets()
    {
        return $this->belongsToMany(Asset::class, 'employee_asset')
            ->withPivot('assigned_date', 'return_date', 'assignment_status', 'assignment_notes')
            ->withTimestamps();
    }


    public function licenses()
    {
        return $this->belongsToMany(License::class, 'employee_license')
            ->withPivot('assigned_date', 'expiry_date', 'status')
            ->withTimestamps();
    }


    public function activeLicenses()
    {
        return $this->belongsToMany(License::class, 'employee_license')
            ->withPivot('assigned_date', 'expiry_date', 'status')
            ->wherePivot('status', 'active')
            ->withTimestamps();
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($words[0], 0, 1));
    }


    public function getAssetsCountAttribute()
    {
        return $this->assets()->count();
    }


    public function getLicensesCountAttribute()
    {
        return $this->licenses()->count();
    }


    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('iqama_id', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%")
              ->orWhere('position', 'like', "%{$search}%");
        });
    }
}
