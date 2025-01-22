<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'total_price',
        'status',
    ];

    // Relacja do pozycji zamówienia
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relacja do adresu
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
