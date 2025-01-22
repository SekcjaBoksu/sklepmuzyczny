<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'rating', 'review'];

    // Relacja do użytkownika
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacja do produktu
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

