<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renter extends Model
{
    protected $table = 'renters';

    protected $fillable = ['title','area','agent','phone','email','status'];
}
