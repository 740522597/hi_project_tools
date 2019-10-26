<?php

namespace App;

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
