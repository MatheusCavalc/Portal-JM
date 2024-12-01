<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoicing extends Model
{
    protected $fillable = [
        'seller_id',
        'nfe_value',
        'bol_value',
        'initial_date',
        'final_date',
        'month_sale',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
