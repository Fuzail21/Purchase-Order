<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // ✅ add this
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ✅ add this too

class Pack extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sizeGroup_id'];

    // Each Pack belongs to a SizeGroup
    public function sizeGroup()
    {
        return $this->belongsTo(SizeGroup::class, 'sizeGroup_id');
    }

    // One Pack has many Sizes
    public function sizes()
    {
        return $this->hasMany(Size::class);
    }

    // One Pack can be used in many PackInformation records
    public function packInformation()
    {
        return $this->hasMany(PackInformation::class);
    }
}

