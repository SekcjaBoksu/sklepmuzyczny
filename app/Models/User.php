<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Dodano pole "role" do masowego wypełniania
    ];

    /**
     * Default attributes for the model.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'client', // Domyślny status klienta
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Obsługa automatycznego hashowania hasła
    ];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

}
