<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $primaryKey = 'contract_id';
    public $incrementing = true;

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'description',
    ];
}
