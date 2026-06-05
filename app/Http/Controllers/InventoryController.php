<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /**
     * Tampilkan daftar produk dengan filter
     */
    public function index(Request $request)
    {
        $query = Produk::with(['kategori', 'supplier']);

        // Filter pencarian
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->byKategori($request->kategori_id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $produks = $query->latest()->paginate(15)->withQueryString();
        $kategoris = Kategori::aktif()->orderBy('nama')->get();

        // inventory.index
        return view('inventory', compact('produks', 'kategoris'));
    }

    // create produk
    public function create()
        {
            $kategoris = Kategori::aktif()->orderBy('nama')->get();
            $suppliers = Supplier::aktif()->orderBy('nama')->get();

            return view('produk.create', compact('kategoris','suppliers'));
        }


     public function store(Request $request)
        {
            $validated = $request->validate([
                'sku'           => 'required|string|max:100|unique:produks,sku',
                'nama'          => 'required|string|max:255',
                'deskripsi'     => 'nullable|string',
                'kategori_id'   => 'required|exists:kategoris,id',
                'supplier_id'   => 'required|exists:suppliers,id',
                'satuan'        => 'required|string|max:50',
                'stok'          => 'required|integer|min:0',
                'stok_minimum'  => 'required|integer|min:0',
                'harga_beli'    => 'required|numeric|min:0',
                'harga_jual'    => 'required|numeric|min:0',
                'lokasi'        => 'nullable|string|max:100',
                'gambar'        => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('gambar')) {
                $validated['gambar'] = $request->file('gambar')->store('produks','public');
            }

            $produk = Produk::create($validated);
            $produk->updateStatus();

            return redirect()->route('inventory.index')
                ->with('success',"Produk {$produk->nama} berhasil ditambahkan.");
        }   

    /**
     * Tampilkan form edit produk
     */
    public function edit(Produk $produk)
    {
        $kategoris = Kategori::aktif()->orderBy('nama')->get();
        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        return view('produk.edit', compact('produk', 'kategoris', 'suppliers'));
    }

    /**
     * Update data produk (tidak bisa tambah/hapus dari inventory)
     */
    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'kategori_id'   => 'required|exists:kategoris,id',
            'supplier_id'   => 'required|exists:suppliers,id',
            'satuan'        => 'required|string|max:50',
            'stok_minimum'  => 'required|integer|min:0',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'lokasi'        => 'nullable|string|max:100',
            'gambar'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('produks', 'public');
        }

        $produk->update($validated);
        $produk->updateStatus();

        return redirect()->route('inventory.index')
            ->with('success', "Produk {$produk->nama} berhasil diperbarui.");
    }

    /**
     * Detail produk + riwayat transaksi
     */
    public function show(Produk $produk)
    {
        $produk->load(['kategori', 'supplier']);
        $riwayat = $produk->transaksis()
            ->latest('tanggal_transaksi')
            ->limit(10)
            ->get();

        return view('inventory.show', compact('produk', 'riwayat'));
    }


    // menghapus produk 
    public function destroy(Produk $produk)
    {
        // hapus gambar jika ada
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $nama = $produk->nama;

        $produk->delete();

        return redirect()->route('inventory.index')
            ->with('success', "Produk {$nama} berhasil dihapus.");
    }

    /**
     * Export inventory ke CSV
     */
    public function export(Request $request)
    {
        $query = Produk::with(['kategori', 'supplier']);

        if ($request->filled('kategori_id')) {
            $query->byKategori($request->kategori_id);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $produks = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($produks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['SKU', 'Nama', 'Kategori', 'Supplier', 'Stok', 'Satuan', 'Min Stok', 'Harga Jual', 'Lokasi', 'Status']);

            foreach ($produks as $p) {
                fputcsv($file, [
                    $p->sku, $p->nama, $p->kategori->nama, $p->supplier->nama,
                    $p->stok, $p->satuan, $p->stok_minimum,
                    $p->harga_jual, $p->lokasi, $p->status_label,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}