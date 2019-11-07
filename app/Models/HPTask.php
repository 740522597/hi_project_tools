<?php

namespace App\Models;

use Carbon\Carbon;
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
        'urgency_level',
        'due_at'
    ];
    public $appends = ['is_pass_due'];

    public function getIsPassDueAttribute()
    {
        if ($this->attributes['due_at'] && Carbon::parse($this->attributes['due_at'])->lt(Carbon::now())) {
            return true;
        }
        return false;
    }
}
