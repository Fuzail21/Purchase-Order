<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ratio extends Model
{
    protected $fillable = ['packI_id','size_name','ratio','actual_qty'];

    public function pack()
    {
        return $this->belongsTo(PackInformation::class, 'pack_id');
    }

}

