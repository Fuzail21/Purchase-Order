<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = ['order_id','color_name','size_group_id'];

    public function order() { return $this->belongsTo(Order::class); }
    public function packs()
    {
        return $this->hasMany(PackInformation::class, 'color_id');
    }
    public function sizeGroup() { return $this->belongsTo(SizeGroup::class); }
}

