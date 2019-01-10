<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $guarded = [];


    public function Bots()
    {
        return $this->hasMany('App\Models\Bot');
    }
}
