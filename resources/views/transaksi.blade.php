@extends('layouts.app')
@section('page-title', 'Transaksi')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Global & Layout */
    .trans-container { font-family: 'Inter', sans-serif; color: #334155; width: 90%; margin: 0 auto; margin-top: 1rem;}
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header-title { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .page-header-sub { font-size: 14px; color: #64748b; }

    /* Stats Grid */
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; }
    .stat-icon-wrap { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .stat-label { font-size: 13px; color: #64748b; font-weight: 500; margin-bottom: 4px; }
    .stat-value { font-size: 22px; font-weight: 800; color: #0f172a; }

    /* Filter Bar */
    .filter-card { background: white; padding: 16px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 16px; }
    .filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
    .form-input { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; background: white; }
    .form-select { cursor: pointer; min-width: 160px; }

    /* Timeline Design */
    .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
    .card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #fafafa; }
    .card-title { font-weight: 700; color: #334155; font-size: 15px; }
    
    .timeline-container { position: relative; padding: 10px 20px; }
    .timeline-line { position: absolute; left: 36px; top: 0; bottom: 0; width: 2px; background: #f1f5f9; }
    
    .timeline-item { display: flex; gap: 20px; padding: 20px 0; border-bottom: 1px solid #f8fafc; position: relative; z-index: 1; }
    .timeline-item:last-child { border-bottom: none; }
    
    .timeline-icon { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative; z-index: 2; box-shadow: 0 0 0 4px white; }
    .icon-masuk { background: #dcfce7; color: #16a34a; border: 2px solid #bbf7d0; }
    .icon-keluar { background: #fee2e2; color: #dc2626; border: 2px solid #fecaca; }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer; border: 1px solid transparent; transition: 0.2s; font-size: 14px; text-decoration: none; }
    .btn-primary { background: #2563eb; color: white; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-outline { background: white; border-color: #e2e8f0; color: #475569; }
    .btn-sm { padding: 7px 14px; font-size: 13px; }
    
    .btn-icon { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 6px; cursor: pointer; transition: 0.2s; }
    .btn-icon-red { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
    .btn-icon-red:hover { background: #dc2626; color: white; }

    .mono { font-family: 'JetBrains Mono', 'Monaco', monospace; font-size: 12px; }
</style>

<div class="trans-container">
    <div class="page-header">
        <div>
            <div class="page-header-title">Transaksi Stok</div>
            <div class="page-header-sub">Riwayat pergerakan barang masuk dan keluar</div>
        </div>
        <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </a>
    </div>

    {{-- Summary Row --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#f0fdf4;">
                <i class="fas fa-arrow-up" style="color:#16a34a;"></i>
            </div>
            <div>
                <div class="stat-label">Total Barang Masuk</div>
                <div style="display:flex; align-items:baseline; gap:5px;">
                    <div class="stat-value">{{ number_format($summary['masuk']) }}</div>
                    <div style="font-size:13px; color:#94a3b8; font-weight:500;">unit</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#fef2f2;">
                <i class="fas fa-arrow-down" style="color:#dc2626;"></i>
            </div>
            <div>
                <div class="stat-label">Total Barang Keluar</div>
                <div style="display:flex; align-items:baseline; gap:5px;">
                    <div class="stat-value">{{ number_format($summary['keluar']) }}</div>
                    <div style="font-size:13px; color:#94a3b8; font-weight:500;">unit</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#eff6ff;">
                <i class="fas fa-arrows-up-down" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-label">Net Movement (30 hari)</div>
                <div style="display:flex; align-items:baseline; gap:5px;">
                    <div class="stat-value">{{ number_format($summary['net']) }}</div>
                    <div style="font-size:13px; color:#94a3b8; font-weight:500;">unit</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-card">
        <form method="GET">
            <div class="filter-bar">
                <select name="tipe" class="form-input form-select">
                    <option value="">Semua Tipe</option>
                    <option value="masuk"  {{ request('tipe')=='masuk' ?'selected':'' }}>Barang Masuk</option>
                    <option value="keluar" {{ request('tipe')=='keluar'?'selected':'' }}>Barang Keluar</option>
                </select>
                <select name="periode" class="form-input form-select">
                    <option value="7"   {{ request('periode','7')=='7'   ?'selected':'' }}>7 Hari Terakhir</option>
                    <option value="30"  {{ request('periode')=='30' ?'selected':'' }}>30 Hari Terakhir</option>
                    <option value="90"  {{ request('periode')=='90' ?'selected':'' }}>3 Bulan Terakhir</option>
                    <option value="all" {{ request('periode')=='all'?'selected':'' }}>Semua</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm" style="background-color: #2563eb; color:white;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['tipe','periode']) && request('periode') !== '7')
                    <a href="{{ route('transaksi.index') }}" style="color:black; font-size: 13px; text-decoration: none; background-color : #F7C300 ; padding:8px; border-radius : 6px ;">Reset</a>
                @endif
                <span style="font-size:13px; color:#94a3b8; margin-left:auto; font-weight:500;">
                    <i class="fas fa-database" style="color:#2563eb; margin-right:4px;"></i>
                    {{ $transaksis->total() }} Transaksi
                </span>
            </div>
        </form>
    </div>

    {{-- Timeline Card --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Riwayat Pergerakan Barang</span>
        </div>

        @if($transaksis->isEmpty())
            <div style="padding:80px 20px; text-align:center; color:#94a3b8;">
                <i class="fas fa-history" style="font-size:40px; display:block; margin-bottom:16px; opacity:0.3;"></i>
                Belum ada data transaksi untuk periode ini.
            </div>
        @else
        <div class="timeline-container">
            <div class="timeline-line"></div>

            @foreach($transaksis as $t)
            <div class="timeline-item">
                {{-- Icon --}}
                <div class="timeline-icon {{ $t->tipe==='masuk'?'icon-masuk':'icon-keluar' }}">
                    <i class="fas fa-{{ $t->tipe==='masuk'?'arrow-up':'arrow-down' }}"></i>
                </div>

                {{-- Content --}}
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px;">
                        <div>
                           <div style="font-size:15px; font-weight:700; color:#0f172a; margin-bottom:2px;">
    {{ optional($t->produk)->nama ?? 'Produk dihapus !' }}
</div>

<div style="font-size:14px; color:#64748b;">
    {{ $t->tipe==='masuk'?'Masuk':'Keluar' }}: 
    <span style="font-weight:700; color:{{ $t->tipe==='masuk'?'#16a34a':'#dc2626' }};">
        {{ $t->tipe==='masuk'?'+':'-' }}{{ number_format($t->jumlah) }} {{ optional($t->produk)->satuan }}
    </span>
</div>
                            
                            {{-- Meta Info --}}
                            <div style="display:flex; flex-wrap:wrap; gap:16px; margin-top:10px; font-size:12px; color:#94a3b8;">
                                <span><i class="fas fa-hashtag"></i> <span class="mono">{{ $t->nomor_referensi }}</span></span>
                                <span><i class="fas fa-user"></i> {{ $t->user_nama }}</span>
                                @if($t->catatan)
                                    <span><i class="fas fa-comment-dots"></i> {{ $t->catatan }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Date & Actions --}}
                        <div style="text-align:right; flex-shrink:0;">
                            <div style="font-size:13px; font-weight:600; color:#1e293b;">{{ $t->tanggal_transaksi->isoFormat('D MMMM YYYY') }}</div>
                            <div style="font-size:12px; color:#94a3b8; margin-top:4px; background:#f8fafc; padding:2px 8px; border-radius:4px; display:inline-block; border:1px solid #f1f5f9;">
                                Stok: {{ $t->stok_sebelum }} <i class="fas fa-chevron-right" style="font-size:10px; margin:0 4px;"></i> {{ $t->stok_sesudah }}
                            </div>
                            
                            <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
                              
                                <form action="{{ route('transaksi.destroy',$t) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Stok produk akan dikembalikan secara otomatis.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-red" title="Hapus">
                                        <i class="fas fa-trash fa-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($transaksis->hasPages())
        <div style="padding:20px; border-top:1px solid #f1f5f9; background: #fafafa;">
            {{ $transaksis->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger text-red-500">
        {{ session('error') }}
    </div>
@endif

@endsection