<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes - Warehouse Management System
|--------------------------------------------------------------------------
*/

// Redirect root ke dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// -----------------------------------------------------------------------
// Semua route memerlukan login (auth) + email verified (dari Breeze)
// -----------------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard — menggantikan default Breeze yang return view('dashboard')
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory - hanya bisa lihat & edit (tidak bisa tambah/hapus produk)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/',              [InventoryController::class, 'index'])->name('index');
        Route::get('/create',        [InventoryController::class, 'create'])->name('create'); 
        Route::post('/',             [InventoryController::class, 'store'])->name('store');   
        Route::get('/export/csv',    [InventoryController::class, 'export'])->name('export');
        Route::get('/{produk}',      [InventoryController::class, 'show'])->name('show');
        Route::get('/{produk}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{produk}',      [InventoryController::class, 'update'])->name('update');
        Route::delete('/inventory/{produk}', [InventoryController::class, 'destroy'])->name('destroy');
    });


    Route::middleware(['admin'])->group(function () {
    Route::resource('users', UserManagementController::class);      
    });
    // Kategori - CRUD lengkap
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/',              [KategoriController::class, 'index'])->name('index');
        Route::post('/',             [KategoriController::class, 'store'])->name('store');
        Route::get('/create', [KategoriController::class, 'create'])->name('create'); 
        Route::put('/{kategori}',    [KategoriController::class, 'update'])->name('update');
        Route::delete('/{kategori}', [KategoriController::class, 'destroy'])->name('destroy');
    });

    // Transaksi - tambah & hapus (tidak ada edit karena mempengaruhi stok)
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/',               [TransaksiController::class, 'index'])->name('index');
        Route::get('/create',         [TransaksiController::class, 'create'])->name('create');
        Route::post('/',              [TransaksiController::class, 'store'])->name('store');
        Route::get('/{transaksi}',    [TransaksiController::class, 'show'])->name('show');
        Route::delete('/{transaksi}', [TransaksiController::class, 'destroy'])->name('destroy');
    });

    // Supplier - CRUD lengkap
    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/',              [SupplierController::class, 'index'])->name('index');
        Route::post('/',             [SupplierController::class, 'store'])->name('store');
        Route::get('/create',        [SupplierController::class, 'create'])->name('create'); 
        Route::put('/{supplier}',    [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',         [LaporanController::class, 'index'])->name('index');
        Route::get('/export-excel',   [LaporanController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export-pdf',   [LaporanController::class, 'exportPDF'])->name('exportPDF');
    });

});

// -----------------------------------------------------------------------
// Profile routes bawaan Laravel Breeze (tetap dipertahankan)
// -----------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';