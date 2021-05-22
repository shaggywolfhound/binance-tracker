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

}
