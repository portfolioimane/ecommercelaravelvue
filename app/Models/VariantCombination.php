<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantCombination extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'combination_values',
        'price',
        'image',
    ];

    protected $casts = [
        'combination_values' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
