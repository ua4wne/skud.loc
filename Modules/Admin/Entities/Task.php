<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //указываем имя таблицы
    protected $table = 'tasks';

    protected $fillable = ['device_id','json','status'];

    /**
     * Роли, принадлежащие действию.
     */
    public function device()
    {
        return $this->hasOne('Modules\Admin\Entities\Device', 'id', 'device_id');
    }
}
