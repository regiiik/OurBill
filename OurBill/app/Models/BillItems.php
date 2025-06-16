<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BillItems extends Model
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
        'bill_id',
        'name',
        'price',
        'qty',
        'total'
    ];
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'bill_item_user')
                    ->withPivot('split_qty', 'split_total')
                    ->withTimestamps();
    }
}
