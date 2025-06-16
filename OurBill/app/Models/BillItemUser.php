<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BillItemUser extends Model
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
        'item_id',
        'user_id',
        'split_qty',
        'split_total',
    ];

    public function billItem()
    {
        return $this->belongsTo(BillItems::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
