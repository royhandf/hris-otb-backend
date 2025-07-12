<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interview extends Model
{
    use HasFactory;

    protected $primaryKey = 'interview_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'interview_id',
        'applicant_id',
        'interviewer_id',
        'schedule',
        'result',
        'notes',
    ];

    public function applicant() {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'interviewer_id', 'employee_id');
    }
}