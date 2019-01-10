<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CellSpeech extends Model
{
    //
    protected $guarded = [];

    public function Cell()
    {
        return $this->belongsTo('App\Models\Cell');
    }
}
