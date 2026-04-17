<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'platform_fee_type',
        'platform_fee_rate',
        'platform_fee_flat',
        'is_verified',
    ];

    protected $casts = [
        'platform_fee_rate' => 'decimal:2',
        'platform_fee_flat' => 'decimal:2',
        'is_verified'       => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'umkm_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'umkm_id');
    }
}
