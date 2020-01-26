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

    public function word_texts()
    {
        $Words = $this->Words;
        $word_texts = [];
        foreach ($Words as $Word) {
            $word_texts[] = $Word->name;
        }
        return join("\n", $word_texts);
    }

    public function reg()
    {
        $Words = $this->Words;
        $word_texts = [];
        foreach ($Words as $Word) {
            $word_texts[] = $Word->name;
        }
        return '('.join("|", $word_texts).')';
    }
}
