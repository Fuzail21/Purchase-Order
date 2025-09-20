<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['job_no', 'style_no', 'po_date', 'ship_date', 'fabrics', 'gsm', 'buyer', 'order_qty', 'title', 'description', 'po_label', 'care_label', 'file_path', 'final_total'];

    // One PO has many PackInformation
    public function packInformation()
    {
        return $this->hasMany(PackInformation::class);
    }
}

