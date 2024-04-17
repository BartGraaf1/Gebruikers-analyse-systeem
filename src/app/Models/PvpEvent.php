<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PvpEvent extends Model
{
    protected $connection = 'external_db';
    protected $table = 'pvp_events_2023_09'; // Adjust the table name as necessary

    // Relationship with PvpViewer
    public function viewer() {
        return $this->belongsTo(PvpViewer::class, 'viewer_id');
    }

    // Disable timestamps if not used
    public $timestamps = false;
}
