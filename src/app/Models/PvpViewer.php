<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PvpViewer extends Model
{
    protected $connection = 'external_db';
    protected $table = 'pvp_viewers_2023_09'; // Adjust the table name as necessary

    // Disable timestamps if not used
    public $timestamps = false;
}
