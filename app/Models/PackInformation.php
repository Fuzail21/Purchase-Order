<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackInformation extends Model
{
    protected $table = 'pack_information';

    protected $fillable = ['color_id','pack_name','pack_qty'];

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function ratios()
    {
        return $this->hasMany(Ratio::class, 'pack_id');
    }
}
