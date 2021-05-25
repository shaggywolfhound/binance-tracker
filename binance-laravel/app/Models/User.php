<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userscurrencies() {
        return $this->hasMany(UsersToCurrencies::class);
    }

    public function currencies()
    {
        return $this->belongsToMany(
            Currencies::class,
            UsersToCurrencies::class,
            'user_id',
            'currency_id',
            'id',
            'id')->withPivot('id', 'currency_id', 'quote_currency_id', 'is_tracked', 'created_at', 'updated_at');
    }

    public function quotecurrencies()
    {
        return $this->belongsToMany(
            Currencies::class,
            UsersToCurrencies::class,
            'user_id',
            'quote_currency_id',
            'id',
            'id')->withPivot('id', 'currency_id', 'quote_currency_id', 'is_tracked', 'created_at', 'updated_at');
    }

    public function currencytickers()
    {
        return $this->hasMany(CurrencyTickers::class, 'user_id', 'id');
    }
}
