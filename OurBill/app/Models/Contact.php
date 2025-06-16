<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contact extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->id = Str::uuid()->toString();
        });
    }

    protected $fillable = [
        'user_id',
        'friend_id'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function Friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
