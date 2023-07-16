<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table        = 'orders';
    protected $primaryKey   = 'order_id';
    protected $fillable     = ['order_code', 'order_date', 'price_amount', 'order_status', 'customer_name', 'customer_phone', 'shipping_address', 'subdistrict', 'district', 'city', 'province', 'postal_code'];

    public function orderDetails()
    {
        return $this->hasMany('App\Models\OrderDetail', 'order_id');
    }

    public function payment()
    {
        return $this->hasOne('App\Models\Payment');
    }
}
