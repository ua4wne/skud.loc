<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracelog extends Model
{
    protected $table = 'tracelogs';

    protected $fillable = ['type','msg'];
}
