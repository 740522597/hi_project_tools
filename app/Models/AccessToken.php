<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = "access_tokens";

    public $fillable = [
        'type',
        'token',
        'expired_at'
    ];

    const TYPE_WECHAT = 'WECHAT';
}
