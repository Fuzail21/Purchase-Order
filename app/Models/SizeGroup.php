<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeGroup extends Model
{
    protected $fillable = ['group_name'];

    public function colors() { return $this->hasMany(Color::class); }
}

