<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    //
    protected $guarded = [];

    public function Bot()
    {
        return $this->belongsTo('App\Models\Bot');
    }

}
