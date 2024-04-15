<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionDailyStat extends Model
{
    protected $casts = [
        'day' => 'date',  // Ensuring 'day' is treated as a date
    ];
    
}
