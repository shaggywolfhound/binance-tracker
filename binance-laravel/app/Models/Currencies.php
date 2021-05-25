<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset',
        'is_quote'
        ];

    protected $guarded = [];

    public $timestamps = true;

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    public function currencyusers()
    {
        return $this->belongsToMany(
            User::class,
            UsersToCurrencies::class,
            'currency_id',
            'user_id',
            'id',
            'id');
    }

    public function quotecurrencyusers()
    {
        return $this->belongsToMany(
            User::class,
            UsersToCurrencies::class,
            'quote_currency_id',
            'user_id',
            'id',
            'id');
    }

}
