<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // ✅ add this
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ✅ add this too

class PackInformation extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_order_id', 'pack_id'];

    // Belongs to a Purchase Order
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Belongs to a Pack
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    // Has many Colors
    public function colors(): HasMany
    {
        return $this->hasMany(Color::class);
    }
}
