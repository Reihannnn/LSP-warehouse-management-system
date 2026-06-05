<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->string('satuan', 50)->default('Unit'); // Unit, Rim, Lusin, dll
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->string('lokasi', 100)->nullable(); // Rak A-1, Gudang B, dll
            $table->enum('status', ['tersedia', 'menipis', 'habis'])->default('tersedia');
            $table->string('gambar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};