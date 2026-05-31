<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'head_id',
        'created_by',
        'status',
    ];

    /**
     * Department head (employee)
     */
    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /**
     * Who created this department.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Employees in this department
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Check if department is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
