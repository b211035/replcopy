<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $guarded = [];

    public function Bot()
    {
        return $this->belongsTo('App\Models\Bot');
    }

    public function Progress()
    {
        return $this->hasMany('App\Models\Progress');
    }

    public function Storage()
    {
        return $this->hasMany('App\Models\Storage');
    }
}
