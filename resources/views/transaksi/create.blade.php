<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>

/* Container */
.trans-container{
    font-family:'Inter',sans-serif;
    color:#334155;
    width:90%;
    margin:auto;
    margin-top:1rem;
}

/* Page Header */
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
    background:#fafafa;
}

.card-title{
    font-weight:700;
    font-size:15px;
}

/* Form */
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

/* Total Box */
.total-box{
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:16px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#f8fafc;
}

.total-label{
    font-size:11px;
    text-transform:uppercase;
    color:#94a3b8;
    font-weight:700;
}

.total-value{
    font-size:26px;
    font-weight:800;
    color:#0f172a;
}

/* Buttons */
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

.form-hint{
    font-size:12px;
    color:#94a3b8;
    margin-top:4px;
}

</style>
<div class="trans-container">

<div class="page-header">
    <div>
        <div class="page-header-title">Tambah Transaksi</div>
        <div class="page-header-sub">Input transaksi stok masuk atau keluar</div>
    </div>

    <a href="{{ route('transaksi.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>


<div class="card">

<div class="card-header">
    <span class="card-title">Form Transaksi</span>
</div>

<form action="{{ route('transaksi.store') }}" method="POST">
@csrf

<div style="padding:24px; display:flex; flex-direction:column; gap:22px;">


<div class="form-grid">

<div class="form-group">
<label class="form-label">Tipe Transaksi</label>
<select name="tipe" class="form-input">
<option value="">-- Pilih Tipe --</option>
<option value="masuk">Stok Masuk</option>
<option value="keluar">Stok Keluar</option>
</select>
</div>

<div class="form-group">
<label class="form-label">Tanggal Transaksi</label>
<input type="datetime-local" name="tanggal_transaksi" class="form-input">
</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Produk</label>

<select name="produk_id" class="form-input">
<option value="">-- Pilih Produk --</option>

@foreach($produks as $produk)
<option value="{{ $produk->id }}">
{{ $produk->nama }} (Stok: {{ $produk->stok }})
</option>
@endforeach

</select>

</div>


<div class="form-group">
<label class="form-label">Supplier</label>

<select name="supplier_id" class="form-input">
<option value="">Tanpa Supplier</option>

@foreach($suppliers as $supplier)
<option value="{{ $supplier->id }}">
{{ $supplier->nama }}
</option>
@endforeach

</select>

<div class="form-hint">
Opsional — untuk transaksi barang masuk
</div>

</div>

</div>


<div class="form-grid">

<div class="form-group">
<label class="form-label">Jumlah</label>
<input type="number" name="jumlah" class="form-input">
</div>

<div class="form-group">
<label class="form-label">Harga Satuan</label>
<input type="number" name="harga_satuan" class="form-input">
</div>

</div>


@if(session('error'))
    <div class="alert alert-danger text-red-500">
        {{ session('error') }}
    </div>
@endif


<div class="form-group">
<label class="form-label">Catatan</label>
<textarea name="catatan" class="form-input" rows="3"></textarea>
</div>


</div>


<div style="padding:20px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between;">

<a href="{{ route('transaksi.index') }}" class="btn btn-outline">
Batal
</a>

<button type="submit" class="btn btn-primary">
<i class="fas fa-save"></i> Simpan Transaksi
</button>

</div>


</form>
</div>

</div>