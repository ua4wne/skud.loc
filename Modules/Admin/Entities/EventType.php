<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    //указываем имя таблицы
    protected $table = 'event_types';

    protected $fillable = ['text','code'];
}
