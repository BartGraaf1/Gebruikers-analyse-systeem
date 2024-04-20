<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionUserAgentStats extends Model
{
    protected $casts = [
        'day' => 'date',  // Ensuring 'day' is treated as a date
    ];

}
