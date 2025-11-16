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

    // Relationships
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    // public function licenses()
    // {
    //     return $this->belongsToMany(License::class, 'employee_license')
    //         ->withPivot('assigned_date', 'expiry_date', 'status')
    //         ->withTimestamps();
    // }

    // Accessors
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        return strtoupper(substr($words[0], 0, 1));
    }

    public function getAssetsCountAttribute()
    {
        return $this->assets()->count();
    }

    // public function getLicensesCountAttribute()
    // {
    //     return $this->licenses()->count();
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
