<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;
    protected $guarded = array();

    public function DocumentTitle()
    {
        return $this->belongsTo(DocumentTitle::class);
    }

    public function Events()
    {
        return $this->hasMany(Event::class);
    }

}
