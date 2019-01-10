<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    //
    protected $table = 'progress';
    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function Scenario()
    {
        return $this->belongsTo('App\Models\Scenario');
    }
    public function Cell()
    {
        return $this->belongsTo('App\Models\ScenarioCell', 'scenario_cell_id');
    }
}
