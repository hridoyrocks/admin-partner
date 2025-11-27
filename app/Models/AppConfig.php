<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    protected $table = 'app_configs';

    protected $fillable = [
        'appStatus',
        'appStatusMessage',
        'enablePremiumSwitch',
        'facebook',
        'latestVersion',
        'forceUpdate',
        'minVersion',
        'notice',
        'noticeAction',
        'privacy',
        'reportReasons',
        'stun',
        'support',
        'teacherNotice',
        'terms',
        'turn',
        'turnName',
        'turnPass',
        'updateInfo',
        'warnings',
        'x',
        'youtube'
    ];

    protected $casts = [
        'latestVersion' => 'integer',
        'minVersion' => 'integer',
        'enablePremiumSwitch' => 'boolean',
        'forceUpdate' => 'boolean',
    ];

    protected $hidden = [
        'turn',
        'stun',
        'turnName',
        'turnPass',
    ];
}
