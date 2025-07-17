<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contract extends Model
{
    protected $primaryKey = 'contract_id';
    public $incrementing = false; // UUID bukan auto-increment
    protected $keyType = 'string'; // UUID adalah string

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->contract_id)) {
                $model->contract_id = (string) Str::uuid();
            }
        });
    }
}
