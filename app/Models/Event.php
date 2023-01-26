<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = array();

    public function DocumentTitle()
    {
        return $this->hasOne(DocumentTitle::class);
    }

    public function ProcessInformation()
    {
        return $this->hasOne(Process::class);
    }

    public function EventInformation()
    {
        return $this->hasOne(EventInformation::class);
    }

    public function Sender()
    {
        return $this->hasOne(Agent::class);
    }

    public function EventAgents()
    {
        return $this->hasMany(EventAgent::class);
    }

    public function Items()
    {
        return $this->hasMany(EventItem::class);
    }

}
