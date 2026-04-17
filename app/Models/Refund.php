<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'reason',
        'status',
        'requested_by',
        'refunded_at',
    ];

    protected $casts = [
        'amount'      => 'float',
        'refunded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Buyer who owns the order (shortcut).
     */
    public function buyer()
    {
        return $this->hasOneThrough(
            User::class,
            Order::class,
            'id',        // orders.id
            'id',        // users.id
            'order_id',  // refunds.order_id
            'buyer_id'   // orders.buyer_id
        );
    }
}
