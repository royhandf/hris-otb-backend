<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $primaryKey = 'applicant_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'applicant_id',
        'name',
        'email',
        'phone',
        'cv_file',
        'applied_position_id',
        'status',
    ];

    // Relasi ke lowongan
    public function jobVacancy()
    {
        return $this->belongsTo(JobVacancy::class, 'applied_position_id', 'vacancy_id');
    }
}