<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    //
    protected $guarded = [];

    public function Bot()
    {
        return $this->belongsTo('App\Models\Bot');
    }

    public function Words()
    {
        return $this->hasMany('App\Models\Word');
    }

}
