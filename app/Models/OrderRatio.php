<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderRatio extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'size_name', 'ratio', 'actual_qty'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
