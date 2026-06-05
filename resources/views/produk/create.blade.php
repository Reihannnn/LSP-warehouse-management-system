@extends('layouts.app')
@section('page-title','Tambah Produk')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>

.prod-container{
width:90%;
margin:auto;
margin-top:1rem;
font-family:'Inter',sans-serif;
}

.form-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
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
}

.card{
background:white;
border-radius:12px;
border:1px solid #e2e8f0;
overflow:hidden;
}

.card-header{
padding:16px 20px;
border-bottom:1px solid #f1f5f9;
background:#fafafa;
font-weight:700;
}

.btn{
padding:10px 18px;
border-radius:8px;
font-weight:600;
border:none;
cursor:pointer;
}

.btn-primary{
background:#2563eb;
color:white;
}

.btn-outline{
border:1px solid #e2e8f0;
background:white;
}

</style>


<div class="prod-container">

<div class="card">

<div class="card-header">
Form Tambah Produk
</div>

<form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div style="padding:24px; display:flex; flex-direction:column; gap:20px;">

<div class="form-grid">

<div class="form-group">
<label class="form-label">SKU</label>
<input type="text" name="sku" class="form-input" required>
</div>

<div class="form-group">
<label class="form-label">Nama Produk</label>
<input type="text" name="nama" class="form-input" required>
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Kategori</label>

<select name="kategori_id" class="form-input" required>

<option value="">Pilih kategori</option>

@foreach($kategoris as $k)
<option value="{{ $k->id }}">{{ $k->nama }}</option>
@endforeach

</select>

</div>


<div class="form-group">
<label class="form-label">Supplier</label>

<select name="supplier_id" class="form-input" required>

<option value="">Pilih supplier</option>

@foreach($suppliers as $s)
<option value="{{ $s->id }}">{{ $s->nama }}</option>
@endforeach

</select>

</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Stok Awal</label>
<input type="number" name="stok" class="form-input" required>
</div>

<div class="form-group">
<label class="form-label">Stok Minimum</label>
<input type="number" name="stok_minimum" class="form-input" required>
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Harga Beli</label>
<input type="number" name="harga_beli" class="form-input" required>
</div>

<div class="form-group">
<label class="form-label">Harga Jual</label>
<input type="number" name="harga_jual" class="form-input" required>
</div>

</div>


<div class="form-group">
<label class="form-label">Satuan</label>
<input type="text" name="satuan" class="form-input" placeholder="Unit / Pcs / Box">
</div>


<div class="form-group">
<label class="form-label">Lokasi Rak</label>
<input type="text" name="lokasi" class="form-input">
</div>


<div class="form-group">
<label class="form-label">Deskripsi</label>
<textarea name="deskripsi" class="form-input"></textarea>
</div>


<div class="form-group">
<label class="form-label">Gambar Produk</label>
<input type="file" name="gambar" class="form-input">
</div>

</div>


<div style="padding:20px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;">

<a href="{{ route('inventory.index') }}" class="btn btn-outline">
Batal
</a>

<button type="submit" class="btn btn-primary">
Simpan Produk
</button>

</div>

</form>

</div>

</div>

@endsection