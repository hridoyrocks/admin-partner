<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRank extends Model
{
    protected $table = 'user_ranks';
    protected $primaryKey = 'userId';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'userId',
        'name',
        'profileUrl',
        'totalDurationLast7Days',
        'totalDurationLast30Days',
        'totalLifetimeDuration',
        'totalCallsLast7Days',
        'totalCallsLast30Days',
        'totalLifetimeCalls',
        'rank',
    ];

    protected $casts = [
        'totalDurationLast7Days' => 'integer',
        'totalDurationLast30Days' => 'integer',
        'totalLifetimeDuration' => 'integer',
        'totalCallsLast7Days' => 'integer',
        'totalCallsLast30Days' => 'integer',
        'totalLifetimeCalls' => 'integer',
        'rank' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'userId', 'uid');
    }

    public function getFormattedWeeklyDurationAttribute()
    {
        return $this->formatDuration($this->totalDurationLast7Days);
    }

    public function getFormattedMonthlyDurationAttribute()
    {
        return $this->formatDuration($this->totalDurationLast30Days);
    }

    public function getFormattedLifetimeDurationAttribute()
    {
        return $this->formatDuration($this->totalLifetimeDuration);
    }

    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }
        return sprintf('%dm', $minutes);
    }
}
