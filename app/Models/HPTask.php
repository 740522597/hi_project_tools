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
        'due_at',
        'notified_at'
    ];
    public $appends  = ['is_pass_due'];

    const TASK_STATUS_PENDING = 'PENDING';
    const TASK_STATUS_DOING = 'DOING';
    const TASK_STATUS_TESTING = 'TESTING';
    const TASK_STATUS_DONE = 'DONE';

    public function getIsPassDueAttribute()
    {
        if ($this->attributes['due_at'] && $this->attributes['status'] != 'DONE' && Carbon::parse($this->attributes['due_at'])->lt(Carbon::now())) {
            return true;
        }
        return false;
    }

    public function plan()
    {
        return $this->belongsTo(HPPlan::class, 'plan_id', 'id');
    }
}
