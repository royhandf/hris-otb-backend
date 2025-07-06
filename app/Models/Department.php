<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'department_id';

    protected $fillable = [
        'name',
        'description',
    ];

    public function employees()
    {
        return $this->hasOne(Employee::class, 'department_id', 'department_id');
    }
}
