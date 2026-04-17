<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static array $categories = [
        'PRODUK_ILEGAL'         => 'Produk Ilegal / Terlarang',
        'PRODUK_BERBAHAYA'      => 'Produk Berbahaya',
        'PENIPUAN'              => 'Penipuan / Scam',
        'KONTEN_TIDAK_PANTAS'   => 'Konten Tidak Pantas',
        'SPAM'                  => 'Spam',
        'LAINNYA'               => 'Lainnya',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::$categories[$this->category] ?? $this->category;
    }
}
