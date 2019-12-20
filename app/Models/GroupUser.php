<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
  //
  protected $guarded = [];
  public function Group()
  {
    return $this->belongsTo('App\Models\Group');
  }
  public function User()
  {
    return $this->belongsTo('App\Models\User');
  }
}
