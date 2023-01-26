<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    use HasFactory;
    protected $guarded = array();
    public $timestamps = false;

    public function ItemType()
    {
        return $this->hasOne(ItemType::class);
    }
    public function ItemState()
    {
        return $this->hasOne(ItemState::class);
    }

    public function OwnerType()
    {
        return $this->hasOne(OwnerType::class);
    }

    public function CoordinationData()
    {
        return $this->hasMany(Coordination::class);
    }

    public function Event()
    {
        return $this->belongsTo(Event::class);
    }
}
