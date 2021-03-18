<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestCard extends Model
{
    protected $table = 'guest_cards';

    protected $fillable = ['card','visitor_id','issued','passed'];

    public $timestamps = false;

    public function visitor()
    {
        return $this->belongsTo('App\Models\Visitor','visitor_id','id');
    }
}
