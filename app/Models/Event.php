<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Entities\EventType;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = ['device_id','event_type','card','flag','event_time','visitor_id'];

    public function device()
    {
        return $this->belongsTo('Modules\Admin\Entities\Device','device_id','id');
    }

    public function eventType()
    {
        return EventType::where(['code'=>$this->event_type])->first();
    }

    public function visitor(){
        return Visitor::find($this->visitor_id);
    }
}
