<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalProduction extends Model
{
    protected $table = 'pvp_productions';
    protected $primaryKey = 'id';
    public $timestamps = false; // Adjust based on your table structure
}
