<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScenarioCell extends Model
{
    // system
    // 0:システム起点 1:システム発話
    // 2:ユーザー起点 3:ユーザー発話
    protected $guarded = [];

    public function Scenario()
    {
        return $this->belongsTo('App\Models\Scenario');
    }

    public function Conditions()
    {
        return $this->hasMany('App\Models\CellCondition');
    }
    public function Speeches()
    {
        return $this->hasMany('App\Models\CellSpeech');
    }
    public function Memorys()
    {
        return $this->hasMany('App\Models\CellMemory');
    }


    public function PrevCells()
    {
        return $this->belongsToMany('App\Models\ScenarioCell', 'cell_chains', 'next_cell_id', 'prev_cell_id');
    }
    public function NextCells()
    {
        return $this->belongsToMany('App\Models\ScenarioCell', 'cell_chains', 'prev_cell_id', 'next_cell_id');
    }

    public function hasNextUserCell()
    {
        return $this->NextCells()->where('system', 3)->exists();
    }
}
