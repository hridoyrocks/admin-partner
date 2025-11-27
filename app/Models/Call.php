<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $table = 'calls';
    protected $primaryKey = 'callId';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'callId',
        'callTime',
        'callerId',
        'callerName',
        'callerProfile',
        'receiverId',
        'receiverName',
        'receiverProfile',
        'duration',
    ];

    protected $casts = [
        'callTime' => 'integer',
        'duration' => 'integer',
    ];

    public function caller()
    {
        return $this->belongsTo(AppUser::class, 'callerId', 'uid');
    }

    public function receiver()
    {
        return $this->belongsTo(AppUser::class, 'receiverId', 'uid');
    }

    public function getFormattedDurationAttribute()
    {
        $seconds = $this->duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }
        return sprintf('%ds', $secs);
    }

    public function getFormattedCallTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->callTime);
    }
}
