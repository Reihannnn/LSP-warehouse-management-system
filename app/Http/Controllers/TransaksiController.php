<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['produk.kategori', 'supplier'])
            ->latest('tanggal_transaksi');

        // Filter tipe
        if ($request->filled('tipe')) {
            $query->byTipe($request->tipe);
        }

        // Filter periode
        $periode = $request->get('periode', '7');
        if ($periode !== 'all') {
            $query->recentDays((int) $periode);
        }

        // Filter tanggal manual
        if ($request->filled('tgl_dari') && $request->filled('tgl_sampai')) {
            $query->byPeriod($request->tgl_dari . ' 00:00:00', $request->tgl_sampai . ' 23:59:59');
        }

        $transaksis = $query->paginate(20)->withQueryString();

        // Summary stats
        $summary = [
            'masuk'  => Transaksi::where('tipe', 'masuk')->recentDays(30)->sum('jumlah'),
            'keluar' => Transaksi::where('tipe', 'keluar')->recentDays(30)->sum('jumlah'),
        ];
        $summary['net'] = $summary['masuk'] - $summary['keluar'];

        // transaksi.index
        return view('transaksi', compact('transaksis', 'summary'));
    }

    public function create()
    {
        $produks = Produk::with(['kategori', 'supplier'])->orderBy('nama')->get();
        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        return view('transaksi.create', compact('produks', 'suppliers'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'tipe'               => 'required|in:masuk,keluar',
        'produk_id'          => 'required|exists:produks,id',
        'supplier_id'        => 'nullable|exists:suppliers,id',
        'jumlah'             => 'required|integer|min:1',
        'harga_satuan'       => 'required|numeric|min:0',
        'catatan'            => 'nullable|string',
        'tanggal_transaksi'  => 'required|date',
    ]);

    try {
        DB::transaction(function () use ($validated) {
            $produk = Produk::lockForUpdate()->findOrFail($validated['produk_id']);

            // Validasi stok cukup untuk keluar
            if (
                $validated['tipe'] === 'keluar' &&
                $produk->stok < $validated['jumlah']
            ) {
                throw new \Exception("Stok tidak mencukupi");
            }

            $stokSebelum = $produk->stok;

            $stokSesudah = $validated['tipe'] === 'masuk'
                ? $stokSebelum + $validated['jumlah']
                : $stokSebelum - $validated['jumlah'];

            Transaksi::create([
                ...$validated,
                'nomor_referensi' => Transaksi::generateNomorReferensi($validated['tipe']),
                'total_harga'     => $validated['jumlah'] * $validated['harga_satuan'],
                'stok_sebelum'    => $stokSebelum,
                'stok_sesudah'    => $stokSesudah,
                'user_id'         => Auth::id(),
                'user_nama'       => Auth::user()->name,
            ]);

            $produk->stok = $stokSesudah;
            $produk->save();
            $produk->updateStatus();
        });

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan.');

    } catch (\Exception $e) {

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['produk.kategori', 'produk.supplier', 'supplier', 'user']);
        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Hapus transaksi - akan rollback stok
     */
    public function destroy(Transaksi $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            $produk = Produk::lockForUpdate()->findOrFail($transaksi->produk_id);

            // Rollback stok
            if ($transaksi->tipe === 'masuk') {
                $produk->stok -= $transaksi->jumlah;
            } else {
                $produk->stok += $transaksi->jumlah;
            }
            $produk->save();
            $produk->updateStatus();

            $transaksi->delete();
        });

        // transaksi.index
        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus dan stok telah di-rollback.');
    }
}