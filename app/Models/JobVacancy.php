<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory;

    protected $primaryKey = 'vacancy_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'vacancy_id',
        'position_id',
        'description',
        'requirements',
        'deadline',
    ];

    // Relasi ke Position
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    // Relasi ke Applicant
    public function applicants()
    {
        return $this->hasMany(Applicant::class, 'applied_position_id', 'vacancy_id');
    }
}
