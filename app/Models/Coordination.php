<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordination extends Model
{
    use HasFactory;
    protected $guarded = array();

    public function CoordinationType()
    {
        return $this->hasOne(CoordinationType::class);
    }

    public function EventItem()
    {
        return $this->belongsTo(EventItem::class);
    }
}
