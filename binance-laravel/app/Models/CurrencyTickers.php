<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyTickers extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_id',
        'user_id',
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
}
