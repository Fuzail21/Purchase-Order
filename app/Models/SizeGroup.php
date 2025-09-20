<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SizeGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // One SizeGroup has many Packs
    public function packs()
    {
        return $this->hasMany(Pack::class, 'sizeGroup_id');
    }
}

