<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    //
    protected $guarded = [];

    public function Bot()
    {
        return $this->belongsTo('App\Models\Bot');
    }

    public function Cells()
    {
        return $this->hasMany('App\Models\ScenarioCell');
    }

    public function Starts()
    {
        return $this->Cells()->whereIn('system', [0, 2]);
    }

    public function SystemStarts()
    {
        return $this->Cells()->where('system', 0);
    }

    public function UserStarts()
    {
        return $this->Cells()->where('system', 2);
    }
}
