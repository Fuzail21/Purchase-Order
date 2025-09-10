<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_no', 'style_no', 'po_date', 'ship_date',
        'fabrics', 'gsm', 'buyer', 'order_qty',
        'file_path', 'body_color', 'pack_id'
    ];

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function ratios()
    {
        return $this->hasMany(OrderRatio::class);
    }

    public function extras()
    {
        return $this->hasMany(OrderExtra::class);
    }
}
