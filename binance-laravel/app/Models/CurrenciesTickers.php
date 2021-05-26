<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrenciesTickers extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_id',
        'quote_currency_id',
        'symbol',
        'price_change',
        'last_price'
    ];

    protected $guarded = [];

    public $timestamps = true;

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function currencies()
    {
        return $this->hasOne(Currencies::class, 'id', 'currency_id');
    }

    public function quotecurrencies()
    {
        return $this->hasOne(Currencies::class, 'id', 'quote_currency_id');
    }
}
