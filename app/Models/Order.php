<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_no','style_no','po_date','ship_date','fabrics',
        'gsm','buyer','order_qty','title','description',
        'po_label','care_label','file_path','final_total'
    ];

    public function colors() { return $this->hasMany(Color::class); }
    public function extras() { return $this->hasMany(Extra::class); }
}
