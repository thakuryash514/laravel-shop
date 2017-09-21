<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'device_uid', 'device_type', 'push_token', 'device_name', 'app_version'
    ];
}
