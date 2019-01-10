<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auther extends Model
{
    //
    protected $fillable = [
        'login_id', 'name', 'password',
    ];
}
