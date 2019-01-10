<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    //
    protected $guarded = [];

    public function Project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function Users()
    {
        return $this->hasMany('App\Models\User');
    }
    public function Scenarios()
    {
        return $this->hasMany('App\Models\Scenario');
    }
    public function Dictionarys()
    {
        return $this->hasMany('App\Models\Dictionary');
    }
    public function Variables()
    {
        return $this->hasMany('App\Models\Variable');
    }
}
