<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;
    protected $guarded = array();

    public function Events()
    {
        return $this->hasMany(Event::class);
    }
}
