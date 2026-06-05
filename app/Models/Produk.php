<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'nama',
        'deskripsi',
        'kategori_id',
        'supplier_id',
        'satuan',
        'stok',
        'stok_minimum',
        'harga_beli',
        'harga_jual',
        'lokasi',
        'status',
        'gambar',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    // Relations
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    // Update status otomatis berdasarkan stok
    public function updateStatus(): void
    {
        if ($this->stok <= 0) {
            $this->status = 'habis';
        } elseif ($this->stok <= $this->stok_minimum) {
            $this->status = 'menipis';
        } else {
            $this->status = 'tersedia';
        }
        $this->save();
    }

    // Scopes
    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'ilike', "%{$keyword}%")
              ->orWhere('sku', 'ilike', "%{$keyword}%");
        });
    }

    // Accessor untuk label status
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'tersedia' => 'Stok Tersedia',
            'menipis'  => 'Stok Menipis',
            'habis'    => 'Stok Habis',
            default    => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'tersedia' => 'success',
            'menipis'  => 'warning',
            'habis'    => 'danger',
            default    => 'secondary',
        };
    }
}