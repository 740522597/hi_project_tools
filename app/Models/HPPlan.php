<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HPPlan extends Model
{
    protected $table    = 'hp_plans';
    public    $fillable = [
        'project_id',
        'title',
        'urgency_level'
    ];
}
