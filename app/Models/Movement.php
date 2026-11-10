<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'occurred_on',
        'supplier',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'occurred_on' => 'date'
    ];

    public function product()
    {
        return $this->hasMany(Product::class)->withTrashed();
    }
}
