@extends('layouts.app')
@section('page-title', 'Tambah Supplier')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>

.sup-container{
    font-family:'Inter',sans-serif;
    color:#334155;
    width:90%;
    margin:auto;
    margin-top:1rem;
}

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

.page-header-title{
    font-size:24px;
    font-weight:700;
    color:#0f172a;
}

.page-header-sub{
    font-size:14px;
    color:#64748b;
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
}

.card-title{
    font-weight:700;
    font-size:15px;
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
    outline:none;
}

.form-input:focus{
    border-color:#2563eb;
}

textarea.form-input{
    resize:vertical;
}

.btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 18px;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
    border:1px solid transparent;
    transition:.2s;
    font-size:14px;
    text-decoration:none;
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
    color:#475569;
}

.error-box{
    background:#fee2e2;
    border:1px solid #fecaca;
    color:#b91c1c;
    padding:12px 16px;
    border-radius:8px;
    margin-bottom:16px;
    font-size:14px;
}

</style>

<div class="sup-container">

<div class="page-header">
    <div>
        <div class="page-header-title">Tambah Supplier</div>
        <div class="page-header-sub">
            Tambahkan supplier baru untuk produk inventory
        </div>
    </div>

    <a href="{{ route('supplier.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>


{{-- Error Validation --}}
@if ($errors->any())
<div class="error-box">
<ul style="margin:0; padding-left:16px;">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif


<div class="card">

<div class="card-header">
<span class="card-title">Form Supplier</span>
</div>

<form action="{{ route('supplier.store') }}" method="POST">
@csrf

<div style="padding:24px; display:flex; flex-direction:column; gap:20px;">


<div class="form-grid">

<div class="form-group">
<label class="form-label">Nama Supplier</label>
<input type="text" name="nama" class="form-input"
value="{{ old('nama') }}" required>
</div>

<div class="form-group">
<label class="form-label">Nama Kontak</label>
<input type="text" name="nama_kontak" class="form-input"
value="{{ old('nama_kontak') }}" required>
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-input"
value="{{ old('email') }}" required>
</div>

<div class="form-group">
<label class="form-label">Telepon</label>
<input type="text" name="telepon" class="form-input"
value="{{ old('telepon') }}" required>
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Kota</label>
<input type="text" name="kota" class="form-input"
value="{{ old('kota') }}">
</div>

<div class="form-group">
<label class="form-label">Status</label>

<select name="aktif" class="form-input">
<option value="1" {{ old('aktif',1)==1?'selected':'' }}>Aktif</option>
<option value="0" {{ old('aktif')==0?'selected':'' }}>Non Aktif</option>
</select>

</div>

</div>


<div class="form-group">
<label class="form-label">Alamat</label>
<textarea name="alamat" rows="3"
class="form-input"
required>{{ old('alamat') }}</textarea>
</div>


<div class="form-group">
<label class="form-label">Catatan</label>
<textarea name="catatan" rows="3"
class="form-input">{{ old('catatan') }}</textarea>
</div>


</div>


<div style="padding:20px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between;">

<a href="{{ route('supplier.index') }}" class="btn btn-outline">
Batal
</a>

<button type="submit" class="btn btn-primary">
<i class="fas fa-save"></i> Simpan Supplier
</button>

</div>


</form>

</div>

</div>

@endsection