<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $periode   = $request->get('periode', 'bulan_ini');
        $kategoriId = $request->get('kategori_id');

        [$tglDari, $tglSampai] = $this->resolvePeriode($periode, $request);

        // Query dasar
        $baseQuery = Transaksi::with('produk.kategori')
            ->byPeriod($tglDari, $tglSampai);

        if ($kategoriId) {
            $baseQuery->whereHas('produk', fn($q) => $q->where('kategori_id', $kategoriId));
        }

        // Summary cards
        $totalMasuk  = (clone $baseQuery)->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = (clone $baseQuery)->where('tipe', 'keluar')->sum('jumlah');
        $nilaiMasuk  = (clone $baseQuery)->where('tipe', 'masuk')->sum('total_harga');
        $nilaiKeluar = (clone $baseQuery)->where('tipe', 'keluar')->sum('total_harga');

        // Tren per hari/minggu
        $tren = $this->getTren($tglDari, $tglSampai, $kategoriId);

        // Distribusi per kategori
        $distribusi = $this->getDistribusiKategori($tglDari, $tglSampai);

        // Produk paling aktif
        $produkAktif = $this->getProdukAktif($tglDari, $tglSampai, $kategoriId);

        $kategoris = Kategori::aktif()->orderBy('nama')->get();

        // Nilai stok saat ini
        $nilaiStok    = Produk::sum(DB::raw('stok * harga_jual'));
        $growthRate   = $this->calcGrowthRate();
        $stockAcc     = 98.5; // placeholder – bisa dari audit log

        // laporan.index
        return view('laporan', compact(
            'totalMasuk', 'totalKeluar', 'nilaiMasuk', 'nilaiKeluar',
            'tren', 'distribusi', 'produkAktif',
            'kategoris', 'periode', 'tglDari', 'tglSampai', 'kategoriId',
            'nilaiStok', 'growthRate', 'stockAcc'
        ));
    }

    // ---------- helpers ----------

    private function resolvePeriode(string $periode, Request $request): array
    {
        return match($periode) {
            'minggu_ini'   => [now()->startOfWeek(), now()->endOfWeek()],
            'bulan_ini'    => [now()->startOfMonth(), now()->endOfMonth()],
            'bulan_lalu'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'tahun_ini'    => [now()->startOfYear(), now()->endOfYear()],
            'custom'       => [
                $request->get('tgl_dari', now()->startOfMonth()),
                $request->get('tgl_sampai', now()),
            ],
            default        => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    private function getTren($dari, $sampai, $kategoriId = null): array
    {
        $query = Transaksi::select(
                DB::raw("DATE(tanggal_transaksi) as tanggal"),
                'tipe',
                DB::raw('SUM(jumlah) as total')
            )
            ->byPeriod($dari, $sampai)
            ->groupBy('tanggal', 'tipe')
            ->orderBy('tanggal');

        if ($kategoriId) {
            $query->whereHas('produk', fn($q) => $q->where('kategori_id', $kategoriId));
        }

        $rows = $query->get();

        $labels = $rows->pluck('tanggal')->unique()->sort()->values();
        $masuk  = [];
        $keluar = [];

        foreach ($labels as $tgl) {
            $masuk[]  = $rows->where('tanggal', $tgl)->where('tipe', 'masuk')->sum('total');
            $keluar[] = $rows->where('tanggal', $tgl)->where('tipe', 'keluar')->sum('total');
        }

        return compact('labels', 'masuk', 'keluar');
    }

    private function getDistribusiKategori($dari, $sampai): array
    {
        return Transaksi::select('kategoris.nama', DB::raw('SUM(transaksis.jumlah) as total'))
            ->join('produks', 'transaksis.produk_id', '=', 'produks.id')
            ->join('kategoris', 'produks.kategori_id', '=', 'kategoris.id')
            ->byPeriod($dari, $sampai)
            ->groupBy('kategoris.nama')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

   private function getProdukAktif($dari, $sampai, $kategoriId = null)
{
    $query = Transaksi::select(
            'produk_id',
            DB::raw('SUM(CASE WHEN tipe=\'masuk\' THEN jumlah ELSE 0 END) as total_masuk'),
            DB::raw('SUM(CASE WHEN tipe=\'keluar\' THEN jumlah ELSE 0 END) as total_keluar')
        )
        ->byPeriod($dari, $sampai)
        ->groupBy('produk_id')
        // Tulis ulang rumusnya di sini:
        ->orderByDesc(DB::raw('SUM(CASE WHEN tipe=\'masuk\' THEN jumlah ELSE 0 END) + SUM(CASE WHEN tipe=\'keluar\' THEN jumlah ELSE 0 END)'))
        ->limit(10)
        ->with('produk.kategori');

    if ($kategoriId) {
        $query->whereHas('produk', fn($q) => $q->where('kategori_id', $kategoriId));
    }

    return $query->get();
}

    private function calcGrowthRate(): float
    {
        $bulanIni  = Transaksi::where('tipe', 'masuk')->whereMonth('tanggal_transaksi', now()->month)->sum('jumlah');
        $bulanLalu = Transaksi::where('tipe', 'masuk')->whereMonth('tanggal_transaksi', now()->subMonth()->month)->sum('jumlah');

        if ($bulanLalu == 0) return 0;
        return round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1);
    }

    public function exportExcel(Request $request)
    {
        [$tglDari, $tglSampai] = $this->resolvePeriode($request->get('periode', 'bulan_ini'), $request);

        $transaksis = Transaksi::with(['produk.kategori', 'supplier'])
            ->byPeriod($tglDari, $tglSampai)
            ->orderBy('tanggal_transaksi')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan_' . now()->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($transaksis) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['Tanggal', 'No. Referensi', 'Tipe', 'Produk', 'Kategori', 'Jumlah', 'Satuan', 'Harga Satuan', 'Total', 'User', 'Catatan']);
            foreach ($transaksis as $t) {
                fputcsv($f, [
                    $t->tanggal_transaksi->format('d/m/Y H:i'),
                    $t->nomor_referensi,
                    strtoupper($t->tipe),
                    $t->produk->nama,
                    $t->produk->kategori->nama,
                    $t->jumlah,
                    $t->produk->satuan,
                    $t->harga_satuan,
                    $t->total_harga,
                    $t->user_nama,
                    $t->catatan,
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    public function exportPDF(Request $request)
{
    [$tglDari, $tglSampai] = $this->resolvePeriode(
        $request->get('periode', 'bulan_ini'),
        $request
    );

    $transaksis = Transaksi::with(['produk.kategori', 'supplier'])
        ->byPeriod($tglDari, $tglSampai)
        ->orderBy('tanggal_transaksi')
        ->get();

    $pdf = Pdf::loadView('laporan.pdf', [
        'transaksis' => $transaksis,
        'tglDari' => $tglDari,
        'tglSampai' => $tglSampai,
    ]);

    $pdf->setPaper('a4', 'landscape');

    return $pdf->download(
        'laporan_' . now()->format('Ymd') . '.pdf'
    );
}
}