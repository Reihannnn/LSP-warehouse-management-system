<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'nama_kontak',
        'email',
        'telepon',
        'alamat',
        'kota',
        'aktif',
        'catatan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // Relations
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    // Accessors
    public function getTotalProdukAttribute(): int
    {
        return $this->produks()->count();
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}