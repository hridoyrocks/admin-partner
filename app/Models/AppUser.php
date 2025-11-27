<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'email',
        'phone',
        'gender',
        'profileUrl',
        'nid',
        'bio',
        'points',
        'registered',
        'status',
        'fcmToken',
        'accountStatus',
        'banReason',
        'totalCalls',
        'totalDuration',
        'followers',
        'password',
    ];

    protected $hidden = [
        'password',
        'fcmToken',
    ];

    protected $casts = [
        'points' => 'integer',
        'registered' => 'integer',
        'totalCalls' => 'integer',
        'totalDuration' => 'integer',
        'followers' => 'integer',
    ];

    public function calls()
    {
        return $this->hasMany(Call::class, 'callerId', 'uid');
    }

    public function receivedCalls()
    {
        return $this->hasMany(Call::class, 'receiverId', 'uid');
    }

    public function rank()
    {
        return $this->hasOne(UserRank::class, 'userId', 'uid');
    }
}
