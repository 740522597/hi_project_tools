<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivedTask extends Model
{
    protected $table = 'archived_tasks';

    public $fillable = [
        'id',
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

    const TASK_STATUS_PENDING = 'PENDING';
    const TASK_STATUS_DOING = 'DOING';
    const TASK_STATUS_TESTING = 'TESTING';
    const TASK_STATUS_DONE = 'DONE';

    public function plan()
    {
        return $this->belongsTo(ArchivedPlan::class, 'plan_id', 'id');
    }
}
