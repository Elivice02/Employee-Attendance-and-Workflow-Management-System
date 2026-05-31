<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenceCounter extends Model
{
    protected $fillable = [
        'type',
        'year',
        'department_code',
        'last_number',
    ];
}
