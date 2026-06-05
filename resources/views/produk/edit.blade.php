@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

.container-edit{
    width:90%;
    margin:auto;
    margin-top:20px;
    font-family:'Inter',sans-serif;
}

/* Header */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.page-title{
    font-size:24px;
    font-weight:700;
}

.page-sub{
    font-size:14px;
    color:#64748b;
}

/* Card */

.card{
    background:white;
    border-radius:12px;
    border:1px solid #e2e8f0;
    overflow:hidden;
}

.card-header{
    padding:16px 20px;
    border-bottom:1px solid #f1f5f9;
    font-weight:600;
}

.card-body{
    padding:25px;
}

/* Grid */

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    margin-bottom:20px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-label{
    font-size:13px;
    font-weight:600;
    margin-bottom:6px;
}

.form-input{
    padding:10px 12px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    font-size:14px;
    outline:none;
}

.form-input:focus{
    border-color:#2563eb;
}

/* Button */

.btn{
    padding:10px 18px;
    border-radius:8px;
    font-size:14px;
    font-weight:600;
    border:none;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.btn-primary{
    background:#2563eb;
    color:white;
}

.btn-primary:hover{
    background:#1d4ed8;
}

.btn-outline{
    border:1px solid #e2e8f0;
    background:white;
}

</style>

<div class="container-edit">

<div class="page-header">
<div>
<div class="page-title">Edit Produk</div>
<div class="page-sub">Perbarui data produk</div>
</div>

<a href="{{ route('inventory.index') }}" class="btn btn-outline">
<i class="fas fa-arrow-left"></i> Kembali
</a>

</div>


<div class="card">

<div class="card-header">
Form Edit Produk
</div>

<div class="card-body">

<form action="{{ route('inventory.update',$produk->id) }}" method="POST" enctype="multipart/form-data">

@csrf
@method('PUT')

<div class="form-grid">

<div class="form-group">
<label class="form-label">SKU</label>
<input type="text" class="form-input" value="{{ $produk->sku }}" disabled>
</div>

<div class="form-group">
<label class="form-label">Nama Produk</label>
<input type="text" name="nama" class="form-input" value="{{ $produk->nama }}">
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Kategori</label>

<select name="kategori_id" class="form-input">

@foreach($kategoris as $k)
<option value="{{ $k->id }}"
@if($produk->kategori_id == $k->id) selected @endif>
{{ $k->nama }}
</option>
@endforeach

</select>

</div>


<div class="form-group">
<label class="form-label">Supplier</label>

<select name="supplier_id" class="form-input">

@foreach($suppliers as $s)
<option value="{{ $s->id }}"
@if($produk->supplier_id == $s->id) selected @endif>
{{ $s->nama }}
</option>
@endforeach

</select>

</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Satuan</label>
<input type="text" name="satuan" class="form-input" value="{{ $produk->satuan }}">
</div>

<div class="form-group">
<label class="form-label">Lokasi</label>
<input type="text" name="lokasi" class="form-input" value="{{ $produk->lokasi }}">
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Stok Minimum</label>
<input type="number" name="stok_minimum" class="form-input" value="{{ $produk->stok_minimum }}">
</div>

<div class="form-group">
<label class="form-label">Harga Beli</label>
<input type="number" name="harga_beli" class="form-input" value="{{ $produk->harga_beli }}">
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Harga Jual</label>
<input type="number" name="harga_jual" class="form-input" value="{{ $produk->harga_jual }}">
</div>

<div class="form-group">
<label class="form-label">Upload Gambar</label>
<input type="file" name="gambar" class="form-input">
</div>

</div>


<div class="form-group">
<label class="form-label">Deskripsi</label>
<textarea name="deskripsi" rows="3" class="form-input">{{ $produk->deskripsi }}</textarea>
</div>


<div style="margin-top:20px;display:flex;justify-content:flex-end;gap:10px">

<a href="{{ route('inventory.index') }}" class="btn btn-outline">
Batal
</a>

<button class="btn btn-primary">
<i class="fas fa-save"></i> Update Produk
</button>

</div>

</form>

</div>

</div>

</div>

@endsection