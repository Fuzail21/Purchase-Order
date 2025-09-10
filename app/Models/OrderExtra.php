<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderExtra extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'name', 'percent', 'value'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
