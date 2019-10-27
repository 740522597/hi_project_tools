<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WechatUserMsg extends Model
{
    protected $table = 'wechat_user_messages';
    public $fillable = [
      'open_id',
      'from_user',
      'to_user',
      'create_time',
      'content',
      'msg_type',
      'msg_id',
      'media_id',
      'pic_url',
      'event'
    ];
}
