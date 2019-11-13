<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $table = 'task_comments';

    public $fillable = [
      'task_id',
      'content',
      'created_by'
    ];
}
