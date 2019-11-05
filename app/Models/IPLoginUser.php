<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class IPLoginUser extends Model
{
    protected $table = 'ip_login_users';

    public $fillable = [
      'ip',
      'user_id',
      'wechat_open_id',
      'last_request_at',
      'login_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
