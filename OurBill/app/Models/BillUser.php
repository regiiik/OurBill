<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BillUser extends Model
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
        'bill_id',
        'user_subtotal',
        'treat_deduction',
        'treat_credit',
        'final_total',
        'is_treater',
        'is_paid'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
