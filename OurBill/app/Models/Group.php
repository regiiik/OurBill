<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
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
        'creator_id',
        'name'
    ];

    public function GroupUser()
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id')
                    ->select(['id','name','username','profile']);
    }

    public function members()
    {
        return $this->belongsToMany(
                    User::class,
                    'group_users',
                    'group_id',
                    'user_id'
                )
                ->select(['users.id','users.name','users.username','users.profile']);
    }
}
