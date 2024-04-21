<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvpFragment extends Model
{
    // Specify the connection name for the external database
    protected $connection = 'external_db';

    // Specify the table name if it's different from the model's pluralized form
    protected $table = 'fragments';

    // Disable timestamps if not used
    public $timestamps = false;
}
