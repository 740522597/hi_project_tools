<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    protected $table = 'notifies';

    public $fillable = [
      'type',
      'content',
      'notified_at',
      'url',
      'hash'
    ];

    const TYPE_TASK_DUE = 'TASK_DUE';

    const NOTIFY_TITLE = [
      self::TYPE_TASK_DUE => 'Hi-Project 任务到期提醒'
    ];
}
