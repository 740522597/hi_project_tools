<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    protected $table = 'uploads';

    public $fillable = [
      'task_id',
      'name',
      'path'
    ];

    public $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::url($this->attributes['path']);
    }
}
