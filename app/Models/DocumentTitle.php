<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Заголовок документа
 * */
    
class DocumentTitle extends Model
{
    use HasFactory;

    protected $guarded = array();

    public function Container()
    {
        return $this->hasOne(Container::class);
    }
}
