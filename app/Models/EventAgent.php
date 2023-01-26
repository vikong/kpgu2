<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAgent extends Model
{
    use HasFactory;

    const SENDER = 1;
    const RECIEVER = 2;
    
    protected $guarded = array();

//    public function Event()
//    {
//        return $this->hasOne(Event::class, 'event_id', 'id');
//    }

    public function Event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

}
