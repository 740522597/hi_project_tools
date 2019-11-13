<?php

namespace App\Models;

use App\TaskComment;
use App\Upload;
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
        'notified_at',
        'created_by'
    ];
    public $appends  = ['is_pass_due', 'sub_tasks_finished'];

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

    public function getSubTasksFinishedAttribute()
    {
        $this->load('sub_tasks');
        $count = 0;
        foreach ($this->sub_tasks as $subTask) {
            if ($subTask->is_finished) {
                $count++;
            }
        }
        return $count;
    }

    public function plan()
    {
        return $this->belongsTo(HPPlan::class, 'plan_id', 'id');
    }

    public function sub_tasks()
    {
        return $this->hasMany(SubTask::class, 'task_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(Upload::class, 'task_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id', 'id');
    }
}
