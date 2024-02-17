<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class);
    }
}