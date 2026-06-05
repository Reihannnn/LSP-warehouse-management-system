<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('produks')->latest()->get();

        $stats = [
            'total_kategori' => $kategoris->count(),
            'total_items'    => $kategoris->sum('produks_count'),
            'rata_rata'      => $kategoris->count() > 0
                ? round($kategoris->avg('produks_count'))
                : 0,
        ];
        // kategori.index
        return view('kategori', compact('kategoris', 'stats'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255|unique:kategoris,nama',
            'deskripsi' => 'nullable|string',
            'warna'     => 'nullable|string|max:20',
        ]);

        $kategori = Kategori::create($validated);

        return redirect()->route('kategori.index')
            ->with('success', "Kategori '{$kategori->nama}' berhasil ditambahkan.");
    }

    public function update(Request $request, Kategori $kategori)
{
    $validated = $request->validate([
        'nama'      => 'required|string|max:255|unique:kategoris,nama,' . $kategori->id,
        'deskripsi' => 'nullable|string',
        'warna'     => 'nullable|string|max:20',
    ]);

    $kategori->update($validated);

    return redirect()
        ->route('kategori.index')
        ->with('success', 'Kategori berhasil diperbarui');
}

    public function destroy(Kategori $kategori)
    {
        if ($kategori->produks()->exists()) {
            return redirect()->route('kategori.index')
                ->with('error', "Kategori '{$kategori->nama}' tidak bisa dihapus karena masih memiliki produk.");
        }

        $nama = $kategori->nama;
        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', "Kategori '{$nama}' berhasil dihapus.");
    }
}