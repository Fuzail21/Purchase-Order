<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Size extends Model
{
    use HasFactory;

    protected $fillable = ['pack_id', 'size_name', 'ratio'];

    // Each Size belongs to a Pack
    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }
}

