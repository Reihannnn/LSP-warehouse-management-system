@extends('layouts.app')
@section('page-title', 'Laporan')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    /* Global & Layout */
    .report-container { font-family: 'Inter', sans-serif; color: #334155; width: 90%; margin: 0 auto; margin-top: 1rem;}
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
    .page-header-title { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .page-header-sub { font-size: 14px; color: #64748b; }

    /* Stats/KPI Grid */
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 14px; }
    .stat-icon-wrap { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .stat-label { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .stat-value { font-size: 20px; font-weight: 800; color: #0f172a; }

    /* Filters */
    .form-input { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; background: white; }
    .form-select { cursor: pointer; }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; border: 1px solid transparent; transition: 0.2s; font-size: 13px; text-decoration: none; }
    .btn-primary { background: #2563eb; color: white; }
    .btn-outline { background: white; border-color: #e2e8f0; color: #475569; }

    /* Charts Layout */
    .charts-grid { display: grid; grid-template-columns: 1fr 340px; gap: 16px; margin-bottom: 24px; }
    .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
    .card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-weight: 700; color: #334155; font-size: 15px; }

    /* Table */
    .table-wrap { overflow-x: auto; }
    .tbl { width: 100%; border-collapse: collapse; }
    .tbl th { background: #f8fafc; padding: 12px 20px; font-size: 12px; font-weight: 600; color: #64748b; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .tbl td { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
    .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
    .badge-blue { background: #eff6ff; color: #2563eb; }

    /* Pie Legend */
    .legend-item { display: flex; align-items: center; justify-content: space-between; font-size: 12px; padding: 4px 0; }
    .dot { width: 8px; height: 8px; border-radius: 50%; }

    @media (max-width: 1024px) {
        .charts-grid, .stat-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="report-container">
    <div class="page-header">
        <div>
            <div class="page-header-title">Laporan & Analitik</div>
            <div class="page-header-sub">Analisis performa inventory dan tren stok</div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <form method="GET" style="display:flex; gap:8px; align-items:center;" id="form-filter">
                <select name="periode" onchange="this.form.submit()" class="form-input form-select">
                    <option value="minggu_ini" {{ $periode=='minggu_ini' ?'selected':'' }}>Minggu Ini</option>
                    <option value="bulan_ini"  {{ $periode=='bulan_ini'  ?'selected':'' }}>Bulan Ini</option>
                    <option value="bulan_lalu" {{ $periode=='bulan_lalu' ?'selected':'' }}>Bulan Lalu</option>
                    <option value="tahun_ini"  {{ $periode=='tahun_ini'  ?'selected':'' }}>Tahun Ini</option>
                    <option value="custom"     {{ $periode=='custom'     ?'selected':'' }}>Rentang Custom</option>
                </select>

                <div id="custom-date" style="{{ $periode==='custom'?'display:flex;':'display:none;' }} gap:6px;">
                    <input type="date" name="tgl_dari" value="{{ $tglDari instanceof \Carbon\Carbon ? $tglDari->format('Y-m-d') : $tglDari }}" class="form-input">
                    <input type="date" name="tgl_sampai" value="{{ $tglSampai instanceof \Carbon\Carbon ? $tglSampai->format('Y-m-d') : $tglSampai }}" class="form-input">
                    <button type="submit" class="btn btn-primary btn-sm">Ok</button>
                </div>

                <select name="kategori_id" onchange="this.form.submit()" class="form-input form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ $kategoriId==$k->id?'selected':'' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('laporan.exportPDF') }}?{{ request()->getQueryString() }}" class="btn btn-outline">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('laporan.exportExcel') }}?{{ request()->getQueryString() }}" class="btn btn-outline">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="stat-grid">    
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:{{ $growthRate>=0?'#f0fdf4':'#fff1f2' }};">
                <i class="fas fa-{{ $growthRate>=0?'trending-up':'trending-down' }}" style="color:{{ $growthRate>=0?'#16a34a':'#e11d48' }};"></i>
            </div>
            <div>
                <div class="stat-label">Pertumbuhan</div>
                <div class="stat-value" style="color:{{ $growthRate>=0?'#16a34a':'#e11d48' }};">
                    {{ $growthRate>=0?'+':'' }}{{ $growthRate }}%
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#dcfce7;">
                <i class="fas fa-box-open" style="color:#16a34a;"></i>
            </div>
            <div>
                <div class="stat-label">Barang Masuk</div>
                <div class="stat-value">{{ number_format($totalMasuk) }} <span style="font-size:12px; color:#94a3b8; font-weight:400;">unit</span></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#fff1f2;">
                <i class="fas fa-truck-loading" style="color:#e11d48;"></i>
            </div>
            <div>
                <div class="stat-label">Barang Keluar</div>
                <div class="stat-value">{{ number_format($totalKeluar) }} <span style="font-size:12px; color:#94a3b8; font-weight:400;">unit</span></div>
            </div>
        </div>
    </div>

    {{-- Table Row --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Produk Paling Aktif (Turnover Tinggi)</span>
        </div>
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th style="text-align:right;">Total Masuk</th>
                        <th style="text-align:right;">Total Keluar</th>
                        <th style="text-align:right;">Volume Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkAktif as $i => $row)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td style="font-weight:700; color:#1e293b;">{{ $row->produk->nama }}</td>
                        <td><span class="badge badge-blue">{{ $row->produk->kategori->nama }}</span></td>
                        <td style="text-align:right; color:#16a34a; font-weight:600;">+{{ number_format($row->total_masuk) }}</td>
                        <td style="text-align:right; color:#e11d48; font-weight:600;">-{{ number_format($row->total_keluar) }}</td>
                        <td style="text-align:right; font-weight:800; color:#0f172a;">{{ number_format($row->total_masuk + $row->total_keluar) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:60px; text-align:center; color:#94a3b8;">Data transaksi tidak ditemukan untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Script tetap sama, hanya memastikan pemanggilan variabel sudah benar --}}
@push('styles')
<style>
:root {
    --pie-color-0:#6366f1; --pie-color-1:#10b981; --pie-color-2:#f59e0b;
    --pie-color-3:#ef4444; --pie-color-4:#3b82f6; --pie-color-5:#8b5cf6;
    --pie-color-6:#ec4899; --pie-color-7:#14b8a6;
}
</style>
@endpush

@push('scripts')
<script>
    // Toggle Custom Date
    document.querySelector('select[name="periode"]').addEventListener('change', function() {
        document.getElementById('custom-date').style.display = this.value === 'custom' ? 'flex' : 'none';
    });

    // Chart Lines
    const tren = @json($tren);
    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: tren.labels,
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: tren.masuk,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Barang Keluar',
                    data: tren.keluar,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, font: { size: 12 } } }
            },
            scales: {
                y: { grid: { borderDash: [5, 5] }, ticks: { font: { size: 11 } } },
                x: { grid: { display: false } }
            }
        }
    });

    // Chart Pie/Doughnut
    const dist = @json($distribusi);
    const PIE_COLORS = ['#6366f1','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6'];
    new Chart(document.getElementById('chartPie'), {
        type: 'doughnut',
        data: {
            labels: dist.map(d => d.nama),
            datasets: [{
                data: dist.map(d => d.total),
                backgroundColor: PIE_COLORS,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
@endsection