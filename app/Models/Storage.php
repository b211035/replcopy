<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    //
    protected $table = 'storage';
    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function Variable()
    {
        return $this->belongsTo('App\Models\Variable');
    }
}
