<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table        = 'payments';
    protected $primaryKey   = 'payment_id';
    protected $fillable     = ['payment_date', 'payment_proof', 'order_id'];

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
