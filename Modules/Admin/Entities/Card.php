<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    //указываем имя таблицы
    protected $table = 'cards';

    protected $fillable = ['code','granted','flags','time_zone_id','share'];

    public function time_zone()
    {
        return $this->hasOne('Modules\Admin\Entities\TimeZone', 'id', 'time_zone_id');
    }
}
