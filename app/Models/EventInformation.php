<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInformation extends Model
{
    use HasFactory;

    const Name="EventInformation";

    protected $guarded = array();

    public function EventType()
    {
        return $this->hasOne(EventType::class);
    }

}
