<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    //указываем имя таблицы
    protected $table = 'time_zones';

    protected $fillable = ['zone','begin','end','days','text'];
}
