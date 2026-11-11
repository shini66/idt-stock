<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'category',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }
}
