<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('produks')->latest()->get();

        $stats = [
            'total_supplier'   => $suppliers->count(),
            'produk_tersuplai' => $suppliers->sum('produks_count'),
            'rata_rata'        => $suppliers->count() > 0
                ? round($suppliers->avg('produks_count'))
                : 0,
        ];
        // supplier.i
        return view('supplier', compact('suppliers', 'stats'));
    }

    public function create()
    {
        return view('supplier.create');
    }   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'         => 'required|string|max:255',
            'nama_kontak'  => 'required|string|max:255',
            'email'        => 'required|email|unique:suppliers,email',
            'telepon'      => 'required|string|max:20',
            'alamat'       => 'required|string',
            'kota'         => 'nullable|string|max:100',
            'catatan'      => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);

        return redirect()->route('supplier.index')
            ->with('success', "Supplier '{$supplier->nama}' berhasil ditambahkan.");
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama'         => 'required|string|max:255',
            'nama_kontak'  => 'required|string|max:255',
            'email'        => 'required|email|unique:suppliers,email,' . $supplier->id,
            'telepon'      => 'required|string|max:20',
            'alamat'       => 'required|string',
            'kota'         => 'nullable|string|max:100',
            'aktif'        => 'boolean',
            'catatan'      => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('supplier.index')
            ->with('success', "Supplier '{$supplier->nama}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->produks()->exists()) {
            return redirect()->route('supplier.index')
                ->with('error', "Supplier '{$supplier->nama}' tidak bisa dihapus karena masih memiliki produk terkait.");
        }

        $nama = $supplier->nama;
        $supplier->delete();

        return redirect()->route('supplier.index')
            ->with('success', "Supplier '{$nama}' berhasil dihapus.");
    }
}