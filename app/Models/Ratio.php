<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ratio extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'color_id',
        'size_id',
        'qty',
    ];

    /**
     * Get the color that this ratio belongs to.
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Get the size that this ratio belongs to.
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}