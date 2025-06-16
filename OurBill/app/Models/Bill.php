<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bill extends Model
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
      'name',
      'subtotal',
      'tax',
      'total_amount',
      'treat_percentage',
      'created_by'
    ];

    public function User()
    {
        return $this->hasMany(BillUser::class);
    }

    public function items()
    {
        return $this->hasMany(BillItems::class);
    }

    public function participants()
    {
        return $this->hasMany(BillUser::class);
    }

}
