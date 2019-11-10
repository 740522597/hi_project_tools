<?php

namespace App;

use App\Models\HPProject;
use Illuminate\Database\Eloquent\Model;

class ArchivedPlan extends Model
{
    protected $table    = 'archived_plans';
    public    $fillable = [
        'id',
        'project_id',
        'title',
        'urgency_level'
    ];

    public function project()
    {
        return $this->belongsTo(HPProject::class, 'project_id', 'id');
    }
}
