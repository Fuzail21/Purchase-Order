<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AddOn extends Model
{
    use HasFactory;

    protected $fillable = ['pack_information_id', 'name'];

}

