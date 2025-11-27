<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallTopic extends Model
{
    protected $table = 'call_topics';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
    ];
}
