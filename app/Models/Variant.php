<?php
// app/Models/Variant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type']; // Allow 'name' and 'type' to be mass assignable
    public function variantValues()
    {
        return $this->hasMany(VariantValue::class);
    }
}
