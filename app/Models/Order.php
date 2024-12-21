<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Define which fields are mass assignable
    protected $fillable = [
        'user_id', 
        'status', 
        'name', 
        'email', 
        'phone', 
        'address', 
        'payment_method', 
        'total'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the OrderItem model
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
