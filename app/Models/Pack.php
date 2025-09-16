<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ratios(): HasMany
    {
        return $this->hasMany(Ratio::class, 'packI_id');
    }
}
