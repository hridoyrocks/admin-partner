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

    /**
     * Get duration in seconds (handles both milliseconds and seconds stored values)
     */
    public function getDurationInSecondsAttribute()
    {
        $duration = $this->duration;

        // If duration > 10000, it's likely in milliseconds (old data)
        // A 10000 second call = ~2.7 hours, which is rare
        // But 10000 ms = 10 seconds, which is common
        if ($duration > 10000) {
            return (int) ($duration / 1000);
        }

        return (int) $duration;
    }

    public function getFormattedDurationAttribute()
    {
        $seconds = $this->duration_in_seconds;
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
        $callTime = $this->callTime;

        // If callTime > 9999999999, it's in milliseconds (13 digits)
        if ($callTime > 9999999999) {
            $callTime = (int) ($callTime / 1000);
        }

        return date('Y-m-d H:i:s', $callTime);
    }
}
