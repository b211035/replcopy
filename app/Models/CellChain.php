<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CellChain extends Model
{
  //
  protected $guarded = [];
  public $timestamps = false;

  public function prevCell()
  {
    return $this->belongsTo('App\Models\ScenarioCell', 'prev_cell_id');
  }

  public function nextCell()
  {
    return $this->belongsTo('App\Models\ScenarioCell', 'next_cell_id');
  }

}
