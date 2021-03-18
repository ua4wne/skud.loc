<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    //указываем имя таблицы
    protected $table = 'devices';

    protected $fillable = ['type','snum','fware','conn_fw','image','text','is_active','mode','time_zone_id','address'];


    public function time_zone()
    {
        return $this->hasOne('Modules\Admin\Entities\TimeZone', 'id', 'time_zone_id');
    }

    public function tasks()
    {
        return $this->belongsToMany('Modules\Admin\Entities\Task');
    }

    public function getFullTypeAttribute()
    {
        return "{$this->type} : {$this->text}";
    }
}
