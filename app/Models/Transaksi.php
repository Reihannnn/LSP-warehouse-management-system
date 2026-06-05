<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_referensi',
        'tipe',
        'produk_id',
        'supplier_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'stok_sebelum',
        'stok_sesudah',
        'user_nama',
        'user_id',
        'catatan',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    // Relations
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Generate nomor referensi otomatis
    public static function generateNomorReferensi(string $tipe): string
{
    $prefix = $tipe === 'masuk' ? 'PO' : 'DO';
    $year = now()->year;

    $last = self::withTrashed()
    ->where(
        'nomor_referensi',
        'like',
        "{$prefix}-{$year}-%"
    )
    ->orderByDesc('id')
    ->first();

    $next = 1;

    if ($last) {
        $parts = explode('-', $last->nomor_referensi);
        $next = ((int) end($parts)) + 1;
    }

    return sprintf(
        '%s-%s-%03d',
        $prefix,
        $year,
        $next
    );
}

    // Scopes
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('tanggal_transaksi', [$start, $end]);
    }

    public function scopeRecentDays($query, $days = 7)
    {
        return $query->where('tanggal_transaksi', '>=', now()->subDays($days));
    }
}