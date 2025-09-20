<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // ✅ add this
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ✅ add this too

class Color extends Model
{
    use HasFactory;

    protected $fillable = ['pack_information_id', 'color_name', 'qty', 'extra_usage_qty'];

    // Belongs to PackInformation
    public function packInformation()
    {
        return $this->belongsTo(PackInformation::class);
    }   
    // Belongs to AddOn
    public function addOn(): BelongsTo
    {
        return $this->belongsTo(AddOn::class);
    }


    // ✅ One color has many ratios
    public function ratios(): HasMany
    {
        return $this->hasMany(Ratio::class);
    }
}
