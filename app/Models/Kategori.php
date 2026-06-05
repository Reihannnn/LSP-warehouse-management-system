<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'deskripsi',
        'warna',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // Relations
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    // Accessors
    public function getTotalItemsAttribute(): int
    {
        return $this->produks()->count();
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}