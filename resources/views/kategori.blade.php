@extends('layouts.app')
@section('page-title', 'Kategori')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    /* Global & Layout */
    .kat-container { font-family: 'Inter', sans-serif; color: #334155; width:90%; margin: 0 auto; margin-top: 1rem;}
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header-title { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .page-header-sub { font-size: 14px; color: #64748b; }

    /* Stats Grid */
    .stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; }
    .stat-icon-wrap { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .stat-label { font-size: 13px; color: #64748b; font-weight: 500; }
    .stat-value { font-size: 20px; font-weight: 700; color: #0f172a; }

    /* Category Cards */
    .kat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
    .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; transition: transform 0.2s, box-shadow 0.2s; position: relative; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

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
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
    .modal-box { background: white; border-radius: 12px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; animation: modalIn 0.3s ease-out; }
    @keyframes modalIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .modal-head { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .modal-head h3 { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; }
    .modal-close { background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; }
    .modal-body { padding: 20px; }
    .modal-foot { padding: 16px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px; }

    /* Form Styling */
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    .form-input { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
    .form-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    .mono { font-family: 'Monaco', 'Consolas', monospace; }
</style>

<div class="kat-container">
    <div class="page-header">
        <div>
            <div class="page-header-title">Manajemen Kategori</div>
            <div class="page-header-sub">Kelola kategori produk inventory Anda</div>
        </div>
        <a href="{{ route('kategori.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    {{-- Summary Row --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#eff6ff;">
                <i class="fas fa-tags" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-label">Total Kategori</div>
                <div class="stat-value">{{ $stats['total_kategori'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#f0fdf4;">
                <i class="fas fa-boxes-stacked" style="color:#16a34a;"></i>
            </div>
            <div>
                <div class="stat-label">Total Items</div>
                <div class="stat-value">{{ $stats['total_items'] }}</div>
            </div>
        </div>
      
    </div>

    {{-- Category Grid --}}
    <div class="kat-grid">
        @forelse($kategoris as $kat)
        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <div style="width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:{{ $kat->warna }}20;">
                    <i class="fas fa-tag" style="color:{{ $kat->warna }}; font-size:16px;"></i>
                </div>
                <div style="display:flex; gap:8px;">

    <button
    type="button"
    class="btn-icon"
    style="background:#eff6ff; color:#2563eb; border:1px solid #dbeafe;"
    title="Edit"
    data-id="{{ $kat->id }}"
    data-nama="{{ $kat->nama }}"
    data-deskripsi="{{ $kat->deskripsi }}"
    data-warna="{{ $kat->warna }}"
    onclick="openEditKategori(this)"
>
    <i class="fas fa-pen fa-xs"></i>
</button>

    <form action="{{ route('kategori.destroy', $kat) }}"
          method="POST"
          onsubmit="return confirm('Hapus kategori {{ addslashes($kat->nama) }}?')">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn-icon btn-icon-red"
                title="Hapus">
            <i class="fas fa-trash fa-xs"></i>
        </button>
    </form>
</div>
            </div>

            <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:6px;">{{ $kat->nama }}</h3>
            <p style="font-size:13px; color:#64748b; line-height:1.5; min-height:40px;">{{ $kat->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:16px; padding-top:12px; border-top:1px solid #f1f5f9;">
                <span style="font-size:12px; font-weight:500; color:#94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Total Items</span>
                <span style="font-size:18px; font-weight:800; color:{{ $kat->warna }};">{{ $kat->produks_count }}</span>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1; padding:80px 20px; text-align:center; background:white; border-radius:12px; border:1px dashed #cbd5e1;">
            <i class="fas fa-folder-open" style="font-size:40px; color:#cbd5e1; margin-bottom:16px; display:block;"></i>
            <span style="color:#64748b; font-size:14px;">Belum ada kategori. Klik <strong>Tambah Kategori</strong> untuk memulai.</span>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal Tambah & Edit Tetap Menggunakan Struktur Kamu Tapi dengan Style Baru --}}
{{-- ... (Struktur Modal Tambah & Edit tetap sama seperti kode awal kamu) ... --}}
<div class="modal-overlay" id="modal-edit">
    <div class="modal-box" style="max-width:500px;">

        <div class="modal-head">
            <h3>Edit Kategori</h3>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('modal-edit')">
                &times;
            </button>
        </div>

        <form id="form-edit-kat" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-body">

                <div class="form-group">
                    <label class="form-label">
                        Nama Kategori
                    </label>

                    <input
                        type="text"
                        name="nama"
                        id="ek-nama"
                        class="form-input"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        id="ek-desk"
                        rows="4"
                        class="form-input"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Warna
                    </label>

                    <input
                        type="color"
                        name="warna"
                        id="ek-warna"
                        class="form-input"
                        style="height:50px;">

                    <div
                        id="ek-warna-hex"
                        class="mono"
                        style="margin-top:8px;">
                    </div>
                </div>

            </div>

            <div class="modal-foot">

                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="closeModal('modal-edit')">
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary">
                    Simpan Perubahan
                </button>

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

    document.getElementById('color-new')?.addEventListener('input', function() {
    document.getElementById('color-new-hex').textContent = this.value;
});

    function openEditKategori(btn)
{
    document.getElementById('form-edit-kat').action =
        '/kategori/' + btn.dataset.id;

    document.getElementById('ek-nama').value =
        btn.dataset.nama;

    document.getElementById('ek-desk').value =
        btn.dataset.deskripsi || '';

    document.getElementById('ek-warna').value =
        btn.dataset.warna || '#6366f1';

    document.getElementById('ek-warna-hex').textContent =
        btn.dataset.warna || '#6366f1';

    openModal('modal-edit');
}

    document.getElementById('ek-warna')?.addEventListener('input', function () {
    document.getElementById('ek-warna-hex').textContent = this.value;
});

</script>
@endpush
@endsection