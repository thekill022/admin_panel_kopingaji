<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'whatsapp',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_verified'       => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'SUPERADMIN';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['SUPERADMIN', 'ADMIN']);
    }

    public function isOwner(): bool
    {
        return $this->role === 'OWNER';
    }

    public function umkms()
    {
        return $this->hasMany(Umkm::class, 'owner_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'owner_id');
    }
}
