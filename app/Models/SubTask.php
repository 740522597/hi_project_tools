<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    protected $table = 'sub_tasks';

    public $fillable = [
      'task_id',
      'title',
      'is_finished',
      'level',
      'created_by'
    ];
}
