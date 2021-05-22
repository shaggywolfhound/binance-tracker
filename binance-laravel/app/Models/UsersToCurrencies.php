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
    ];

    protected $guarded = [];

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
