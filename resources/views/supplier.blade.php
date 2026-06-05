@extends('layouts.app')
@section('page-title', 'Supplier')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Global & Layout */
    .sup-container { font-family: 'Inter', sans-serif; color: #334155; width: 90%; margin: 0 auto;margin-top: 1rem;}
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header-title { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .page-header-sub { font-size: 14px; color: #64748b; }

    /* Stats Grid */
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; }
    .stat-icon-wrap { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .stat-label { font-size: 13px; color: #64748b; font-weight: 500; }
    .stat-value { font-size: 20px; font-weight: 700; color: #0f172a; }

    /* Supplier Cards */
    .sup-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 16px; }
    .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; transition: all 0.2s; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer; border: 1px solid transparent; transition: 0.2s; font-size: 14px; text-decoration: none; }
    .btn-primary { background: #2563eb; color: white; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-outline { background: white; border-color: #e2e8f0; color: #475569; }
    .btn-outline:hover { background: #f8fafc; }
    .btn-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; cursor: pointer; }
    .btn-icon-red { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
    .btn-icon-red:hover { background: #fee2e2; }

    /* Modal Styling */
    .modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; backdrop-filter: blur(4px); }
    .modal-box { background: white; border-radius: 12px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; animation: modalIn 0.3s ease-out; }
    @keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .modal-head { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .modal-head h3 { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; }
    .modal-close { background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; }
    .modal-body { padding: 20px; }
    .modal-foot { padding: 16px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px; }

    /* Form Styling */
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    .form-input { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; }
    .form-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    
    .contact-item { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #64748b; margin-bottom: 8px; }
    .contact-item i { color: #94a3b8; width: 16px; text-align: center; }
</style>

<div class="sup-container">
    <div class="page-header">
        <div>
            <div class="page-header-title">Manajemen Supplier</div>
            <div class="page-header-sub">Kelola data supplier dan pemasok inventory Anda</div>
        </div>
        <a href="{{ route('supplier.create') }}" class="btn btn-primary">
          <button onclick="openModal('modal-tambah-sup')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Supplier
          </button>
        </a>
    </div>

    {{-- Summary --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#eff6ff;">
                <i class="fas fa-building" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-label">Total Supplier</div>
                <div class="stat-value">{{ $stats['total_supplier'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#f0fdf4;">
                <i class="fas fa-boxes-stacked" style="color:#16a34a;"></i>
            </div>
            <div>
                <div class="stat-label">Produk Tersuplai</div>
                <div class="stat-value">{{ $stats['produk_tersuplai'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#fff7ed;">
                <i class="fas fa-chart-bar" style="color:#ea580c;"></i>
            </div>
            <div>
                <div class="stat-label">Rata-rata Produk</div>
                <div class="stat-value">{{ $stats['rata_rata'] }}</div>
            </div>
        </div>
    </div>

    {{-- Supplier Grid --}}
    <div class="sup-grid">
        @forelse($suppliers as $sup)
        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:16px;">
                <div style="width:44px; height:44px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-industry" style="color:#475569; font-size:18px;"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:16px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $sup->nama }}</div>
                    <div style="font-size:13px; color:#2563eb; font-weight:500;">P.I.C: {{ $sup->nama_kontak }}</div>
                </div>
                <div style="display:flex; gap:6px;">
                    <button onclick='openEditSupplier(@json($sup))' class="btn-icon btn-outline" title="Edit">
                        <i class="fas fa-pen fa-xs"></i>
                    </button>
                    <form action="{{ route('supplier.destroy',$sup) }}" method="POST" onsubmit="return confirm('Hapus supplier {{ addslashes($sup->nama) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon btn-icon-red" title="Hapus">
                            <i class="fas fa-trash fa-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $sup->email }}</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>{{ $sup->telepon }}</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-location-dot"></i>
                    <span style="line-height:1.4;">{{ $sup->alamat }} {{ $sup->kota ? '('.$sup->kota.')' : '' }}</span>
                </div>
            </div>

            <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid #f1f5f9;">
                <span style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase;">Kontribusi Stok</span>
                <span style="font-size:13px; font-weight:700; color:#16a34a; background:#f0fdf4; padding:4px 12px; border-radius:20px; border:1px solid #dcfce7;">{{ $sup->produks_count }} Produk</span>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1; padding:80px 20px; text-align:center; background:white; border-radius:12px; border:1px dashed #cbd5e1;">
            <i class="fas fa-truck-ramp-box" style="font-size:40px; color:#cbd5e1; margin-bottom:16px; display:block;"></i>
            <span style="color:#64748b; font-size:14px;">Belum ada supplier terdaftar.</span>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah-sup" class="modal-overlay">
    <div class="modal-box" style="max-width:550px;">
        <div class="modal-head">
            <h3>Tambah Supplier Baru</h3>
            <button class="modal-close" onclick="closeModal('modal-tambah-sup')"><i class="fas fa-xmark"></i></button>
        </div>
        <form action="{{ route('supplier.store') }}" method="POST">
            @csrf
            <div class="modal-body" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div style="grid-column: 1/-1;">
                    <label class="form-label">Nama Perusahaan <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="nama" required placeholder="cth: PT Maju Jaya" class="form-input">
                </div>
                <div>
                    <label class="form-label">Nama Kontak P.I.C <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="nama_kontak" required placeholder="Nama personil" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email Kantor <span style="color:#dc2626;">*</span></label>
                    <input type="email" name="email" required placeholder="supplier@mail.com" class="form-input">
                </div>
                <div>
                    <label class="form-label">Nomor Telepon <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="telepon" required placeholder="021-xxxx" class="form-input">
                </div>
                <div>
                    <label class="form-label">Kota</label>
                    <input type="text" name="kota" placeholder="Jakarta" class="form-input">
                </div>
                <div style="grid-column: 1/-1;">
                    <label class="form-label">Alamat Lengkap <span style="color:#dc2626;">*</span></label>
                    <textarea name="alamat" rows="2" required class="form-input" placeholder="Jl. Raya No. 123..."></textarea>
                </div>
                <div style="grid-column: 1/-1;">
                    <label class="form-label">Catatan Internal</label>
                    <textarea name="catatan" rows="2" class="form-input" placeholder="Opsional..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-tambah-sup')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Supplier</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit (Struktur Sama) --}}
<div id="modal-edit-sup" class="modal-overlay">
    <div class="modal-box" style="max-width:550px;">
        <div class="modal-head">
            <h3>Edit Data Supplier</h3>
            <button class="modal-close" onclick="closeModal('modal-edit-sup')"><i class="fas fa-xmark"></i></button>
        </div>
        <form id="form-edit-sup" method="POST">
            @csrf @method('PUT')
            <div class="modal-body" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div style="grid-column:1/-1;">
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" id="es-nama" name="nama" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Nama Kontak</label>
                    <input type="text" id="es-kontak" name="nama_kontak" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" id="es-email" name="email" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Telepon</label>
                    <input type="text" id="es-telp" name="telepon" class="form-input">
                </div>
                <div>
                    <label class="form-label">Kota</label>
                    <input type="text" id="es-kota" name="kota" class="form-input">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Alamat</label>
                    <textarea id="es-alamat" name="alamat" rows="2" class="form-input"></textarea>
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Catatan</label>
                    <textarea id="es-catatan" name="catatan" rows="2" class="form-input"></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit-sup')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    
    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', e => { if(e.target === el) el.style.display = 'none'; });
    });

    function openEditSupplier(s) {
        document.getElementById('form-edit-sup').action = '/supplier/' + s.id;
        document.getElementById('es-nama').value    = s.nama;
        document.getElementById('es-kontak').value  = s.nama_kontak;
        document.getElementById('es-email').value   = s.email;
        document.getElementById('es-telp').value    = s.telepon;
        document.getElementById('es-kota').value    = s.kota || '';
        document.getElementById('es-alamat').value  = s.alamat;
        document.getElementById('es-catatan').value = s.catatan || '';
        openModal('modal-edit-sup');
    }
</script>
@endpush
@endsection