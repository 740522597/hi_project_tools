<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HPProject extends Model
{
    protected $table = 'hp_projects';

    public $fillable = [
      'name',
      'description',
      'prefix'
    ];
}
