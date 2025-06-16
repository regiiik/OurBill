<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
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

    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'provider',
        'profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function Contact(){
        return $this->hasMany(Contact::class);
    }

    public function GroupUser(){
        return $this->hasMany(GroupUser::class);
    }
    public function Group(){
        return $this->hasMany(Group::class);
    }
    public function bills(){
        return $this->hasMany(Bill::class);
    }

    public function billUsers()
    {
        return $this->hasMany(BillUser::class);
    }

    public function billItems()
    {
        return $this->belongsToMany(BillItems::class, 'bill_item_user')
                    ->withPivot('split_qty', 'split_total')
                    ->withTimestamps();
    }
}
