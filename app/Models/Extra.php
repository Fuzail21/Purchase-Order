<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $table = 'overall_extras';
    protected $fillable = ['order_id','name','percent','value'];

    public function order() { return $this->belongsTo(Order::class); }
}

