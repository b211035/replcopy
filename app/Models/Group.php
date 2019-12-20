<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
  //
  protected $guarded = [];

  public function Bot()
  {
    return $this->belongsTo('App\Models\Bot');
  }
  
  public function Users()
  {
    return $this->belongsToMany('App\Models\User', 'group_users', 'group_id', 'user_id');
  }

  public function Progress()
  {
    return $this->hasMany('App\Models\Progress');
  }
}
