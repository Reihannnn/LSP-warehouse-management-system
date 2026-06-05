<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── 1. KPI CARDS ──────────────────────────────────────────────────────
        $totalProduk    = Produk::count();
        $stokMenipis    = Produk::where('status', 'menipis')->count();
        $stokHabis      = Produk::where('status', 'habis')->count();
        $stokTerbanyak = Produk::max('stok');
        $totalKategori  = Kategori::count();
        $produkStokTerbanyak = Produk::orderByDesc('stok')->first();
        $totalSupplier  = Supplier::count();

        // Nilai inventory = total (stok × harga_jual) semua produk
        $nilaiInventory = Produk::sum(DB::raw('stok * harga_jual'));

        // Jumlah transaksi yang dibuat hari ini
        $transaksiHariIni = Transaksi::whereDate('tanggal_transaksi', today())->count();

        $stats = [
            'total_produk'       => $totalProduk,
            'stok_menipis'       => $stokMenipis,
            'stok_habis'         => $stokHabis,
            'total_kategori'     => $totalKategori,
            'total_supplier'     => $totalSupplier,
            'nilai_inventory'    => $nilaiInventory,
            'transaksi_hari_ini' => $transaksiHariIni,
            'stok_terbanyak'     => $produkStokTerbanyak?->stok ?? 0,
            'produk_terbanyak'   => $produkStokTerbanyak?->nama ?? '-',
        ];

        // ── 2. BAR CHART — pergerakan stok 7 hari terakhir ───────────────────
        // Menghasilkan collection dengan kolom: tgl (DATE string), tipe, total
        // View me-loop hari D-6 s/d hari ini dan mencari data yang cocok
        $chartData = Transaksi::select(
                DB::raw("DATE(tanggal_transaksi) as tgl"),
                'tipe',
                DB::raw('SUM(jumlah) as total')
            )
            ->where('tanggal_transaksi', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw("DATE(tanggal_transaksi)"), 'tipe')
            ->orderBy(DB::raw("DATE(tanggal_transaksi)"))
            ->get();

        // ── 3. STOK KRITIS — produk habis atau menipis, urut stok terkecil ──
        $produkKritis = Produk::with(['kategori', 'supplier'])
            ->whereIn('status', ['menipis', 'habis'])
            ->orderByRaw("CASE status WHEN 'habis' THEN 0 WHEN 'menipis' THEN 1 ELSE 2 END")
            ->orderBy('stok')
            ->limit(8)
            ->get();

        // ── 4. TRANSAKSI TERAKHIR — 5 transaksi terbaru ──────────────────────
        $transaksiTerakhir = Transaksi::with(['produk'])
            ->latest('tanggal_transaksi')
            ->limit(5)
            ->get();

        $stokTerbanyak = Produk::with(['kategori'])
            ->orderByDesc('stok')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'chartData',
            'produkKritis',
            'transaksiTerakhir',
            'stokTerbanyak'
        ));
    }
}