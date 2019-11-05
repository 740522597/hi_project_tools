<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HiProjectCache extends Model
{
    protected $table = 'hi_project_caches';

    public $fillable = [
      'key',
      'value',
      'status',
      'comment'
    ];
}
