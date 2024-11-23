<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoicing extends Model
{
    protected $fillable = [
        'seller_id',
        'value',
        'initial_date',
        'final_date'
    ];
}
