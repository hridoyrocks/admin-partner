<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'userId',
        'userName',
        'userProfile',
        'reporterId',
        'reporterName',
        'callId',
        'reason',
        'description',
        'status',
        'actionTaken',
        'resolvedBy',
        'time',
    ];

    protected $casts = [
        'time' => 'integer',
    ];

    public function reportedUser()
    {
        return $this->belongsTo(AppUser::class, 'userId', 'uid');
    }

    public function reporter()
    {
        return $this->belongsTo(AppUser::class, 'reporterId', 'uid');
    }
}
