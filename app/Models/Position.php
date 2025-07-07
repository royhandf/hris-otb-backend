<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $primaryKey = 'position_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['position_id', 'name', 'salary', 'level'];

    // Relasi ke JobVacancy
    public function jobVacancies()
    {
        return $this->hasMany(JobVacancy::class, 'position_id', 'position_id');
    }

    public function employees()
    {
        return $this->hasOne(Employee::class, 'position_id', 'position_id');
    }
}
