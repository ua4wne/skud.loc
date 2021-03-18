<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $table = 'visitors';

    protected $fillable = ['fname','mname','lname','image','card','renter_id','car_id','car_num','doc_type_id',
        'doc_series','doc_num','phone','employee'];

    public function renter()
    {
        return $this->hasOne('App\Models\Renter', 'id', 'renter_id');
    }

    public function car()
    {
        return $this->hasOne('App\Models\Car', 'id', 'car_id');
    }

    public function doc_type()
    {
        return $this->hasOne('App\Models\DocType', 'id', 'doc_type_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->lname} {$this->fname} {$this->mname}";
    }
}
