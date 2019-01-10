<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CellCondition extends Model
{
    //
    protected $guarded = [];

    public function Cell()
    {
        return $this->belongsTo('App\Models\Cell');
    }

    public function Variable()
    {
        return $this->belongsTo('App\Models\Variable');
    }
}
