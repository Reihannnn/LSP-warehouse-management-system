@extends('layouts.app')

@section('page-title','Tambah Kategori')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.container{
max-width:500px;
margin:auto;
margin-top:40px;
}

.card{
background:white;
border-radius:10px;
padding:20px;
box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.form-group{
display:flex;
flex-direction:column;
margin-bottom:14px;
}

.form-group input,
.form-group textarea{
padding:8px;
border:1px solid #ddd;
border-radius:6px;
}

.btn{
padding:8px 14px;
border-radius:6px;
border:none;
cursor:pointer;
}

.btn-primary{
background:#2563eb;
color:white;
}

.btn-outline{
border:1px solid #ddd;
background:white;
}
</style>


<div class="container">

<div class="card">

<h3 style="margin-bottom:20px;">Tambah Kategori</h3>

<form action="{{ route('kategori.store') }}" method="POST">
@csrf

<div class="form-group">
<label>Nama Kategori</label>
<input type="text" name="nama" required>
</div>

<div class="form-group">
<label>Deskripsi</label>
<textarea name="deskripsi"></textarea>
</div>

<div class="form-group">
<label>Warna Label</label>
<input type="color" name="warna">
</div>

<div style="display:flex; gap:10px; margin-top:15px;">

<a href="{{ route('kategori.index') }}" class="btn btn-outline">
Batal
</a>

<button type="submit" class="btn btn-primary">
Simpan
</button>

</div>

</form>

</div>

</div>

@endsection