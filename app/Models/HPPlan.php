<?php

namespace App\Models;

use App\Traits\QueryTrait;
use Illuminate\Database\Eloquent\Model;

class HPPlan extends Model
{
    use QueryTrait;

    protected $table    = 'hp_plans';
    public    $fillable = [
        'project_id',
        'title',
        'urgency_level',
        'created_by'
    ];

    public $appends = ['unfinished_count'];

    public function getUnfinishedCountAttribute()
    {
        return $this->myQuery(HPTask::query())
            ->where('plan_id', $this->attributes['id'])
            ->where('status', '<>', 'DONE')
            ->count();
    }

    public function project()
    {
        return $this->belongsTo(HPProject::class, 'project_id', 'id');
    }
}
