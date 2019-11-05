<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HPTask extends Model
{
    protected $table = 'hp_tasks';

    public $fillable = [
        'plan_id',
        'title',
        'description',
        'prefix',
        'code',
        'assign_to',
        'start_at',
        'end_at',
        'estimation',
        'status',
        'urgency_level'
    ];
}
