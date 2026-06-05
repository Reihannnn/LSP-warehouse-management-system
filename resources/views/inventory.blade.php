@extends('layouts.app')
@section('page-title', 'Inventory')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Global Helpers */
    .inventory-container {
       font-family: 'Inter', sans-serif; color: #334155; 
       width: 90%;
       margin: 0 auto;
       margin-top: 1rem;
      }
    .card { background: white; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
    
    /* Header */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .page-header-title { font-size: 24px; font-weight: 700; color: #0f172a; }
    
    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 6px; font-weight: 500; cursor: pointer; text-decoration: none; font-size: 14px; transition: 0.2s; border: 1px solid transparent; }
    .btn-sm { padding: 6px 12px; font-size: 13px; }
    .btn-primary { background: #2563eb; color: white; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-outline { background: white; border-color: #e2e8f0; color: #64748b; }
    .btn-outline:hover { background: #f8fafc; }
    .btn-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
    .btn-icon-blue { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }

    /* Filter Bar */
    .filter-section { padding: 16px; }
    .filter-bar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .search-wrap { position: relative; flex-grow: 1; min-width: 250px; }
    .search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .form-input { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; font-size: 14px; }
    .form-input:focus { border-color: #2563eb; ring: 2px solid #dbeafe; }
    .form-select { background: white; cursor: pointer; }

    /* Table Styles */
    .table-wrap { overflow-x: auto; }
    .tbl { width: 100%; border-collapse: collapse; text-align: left; }
    .tbl th { background: #f8fafc; padding: 12px 20px; font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
    .tbl td { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .mono { font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; }

    /* Badges */
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-yellow { background: #fef9c3; color: #854d0e; }
    .badge-red { background: #fee2e2; color: #991b1b; }

    .empty-state { padding: 60px; text-align: center; color: #94a3b8; }
</style>

<div class="inventory-container">
    <div class="page-header">
        <div class="page-header-title">Inventory</div>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Tambah Produk
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="card filter-section">
        <form method="GET">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..." class="form-input" style="padding-left:35px;">
                </div>
                
                <select name="kategori_id" class="form-input form-select" style="width: 200px;">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected':'' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-input form-select" style="width: 180px;">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status')=='tersedia'?'selected':'' }}>Stok Tersedia</option>
                    <option value="menipis"  {{ request('status')=='menipis' ?'selected':'' }}>Stok Menipis</option>
                    <option value="habis"    {{ request('status')=='habis'   ?'selected':'' }}>Stok Habis</option>
                </select>

                <button type="submit" class="btn btn-primary btn-sm" style="background-color: #2563eb; color:white;">
                    <i class="fas fa-filter"></i> Filter
                </button>

                @if(request()->hasAny(['search','kategori_id','status']))
                    <a href="{{ route('inventory.index') }}" style="color:black; font-size: 13px; text-decoration: none; background-color : #F7C300 ; padding:8px; border-radius : 6px ; ">Reset</a>
                @endif
            </div>
            <div style="margin-top:12px; font-size:12px; color:#94a3b8;">
                <i class="fas fa-info-circle" style="color:#2563eb;"></i> {{ $produks->total() }} items ditemukan
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>SKU</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Lokasi</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $p)
                    <tr>
                        <td>
                            <div style="font-weight:600; color:#1e293b;">{{ $p->nama }}</div>
                            <div style="font-size:11px; color:#94a3b8;">{{ $p->supplier->nama }}</div>
                        </td>
                        <td><span class="mono">{{ $p->sku }}</span></td>
                        <td>{{ $p->kategori->nama }}</td>
                        <td>
                            <div style="font-weight:600;">{{ number_format($p->stok) }} {{ $p->satuan }}</div>
                            <div style="font-size:11px; color:#94a3b8;">Min: {{ $p->stok_minimum }}</div>
                        </td>
                        <td style="color:#64748b;">{{ $p->lokasi ?? '—' }}</td>
                        <td style="font-weight:500;">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                        <td>
                            @if($p->status === 'tersedia')
                                <span class="badge badge-green">Stok Tersedia</span>
                            @elseif($p->status === 'menipis')
                                <span class="badge badge-yellow">Stok Menipis</span>
                            @else
                                <span class="badge badge-red">Stok Habis</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; justify-content: flex-end;">
                                <a href="{{ route('inventory.edit', $p) }}" class="btn-icon btn-icon-blue" title="Edit">
                                    <i class="fas fa-pen fa-sm"></i>
                                </a>
                                <form action="{{ route('inventory.destroy',$p) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus produk ini?')">

                                  @csrf
                                  @method('DELETE')

                                  <button class="btn-icon" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;" title="Hapus">
                                  <i class="fas fa-trash fa-sm"></i>
                                  </button>

                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="fas fa-box-open" style="font-size:32px; display:block; margin-bottom:12px; opacity:0.5;"></i>
                            Tidak ada produk ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produks->hasPages())
        <div style="padding:15px; border-top:1px solid #f1f5f9;">
            {{ $produks->links() }}
        </div>
        @endif
    </div>
</div>

@endsection