<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'amount'                  => 'float',
        'gross_amount'            => 'float',
        'platform_fee_deduction'  => 'float',
        'admin_fee_amount'        => 'float',
        'net_disbursed'           => 'float',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
