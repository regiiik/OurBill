<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GroupUser extends Model
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
        'group_id',
        'user_id'
    ];

    public function Group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
