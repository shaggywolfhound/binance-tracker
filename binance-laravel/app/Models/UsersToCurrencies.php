<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersToCurrencies extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_id',
        'user_id',
        'quote_currency_id',
        'is_tracked',
    ];

    protected $guarded = [];

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function currency()
    {
        return $this->hasOne(Currencies::class,'id', 'currency_id');
    }

    public function quotecurrency()
    {
        return $this->hasOne(Currencies::class, 'id','quote_currency_id');
    }
}
