@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.dash {
     display: flex; flex-direction: column; gap: 14px; padding: 4px 0; width: 90%; margin: 0 auto; margin-top: 1rem;
}

/* KPI Row */
.kpi-row { display: grid; grid-template-columns: repeat(4,  minmax(0, 1fr)); gap: 12px; }
.kpi { background: #fff; border: 0.5px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; }
.kpi-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.kpi-label { font-size: 11.5px; color: #64748b; margin-bottom: 3px; font-weight: 500; }
.kpi-value { font-size: 22px; font-weight: 600; color: #0f172a; line-height: 1.1; }
.kpi-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* Mid row */
.mid-row { display: grid; grid-template-columns: 1fr 230px; gap: 12px; align-items: center; }
.card { background: #fff; border: 0.5px solid #e2e8f0; border-radius: 12px; }
.card-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 0.5px solid #f1f5f9; }
.card-title { font-size: 13px; font-weight: 600; color: #0f172a; }
.card-link { font-size: 12px; color: #2563eb; text-decoration: none; font-weight: 500; }
.card-link:hover { text-decoration: underline; }
.chart-wrap { padding: 16px 18px; }

/* Side column */
.side-col { display: flex; flex-direction: column; gap: 10px; }
.mini-stat { background: #fff; border: 0.5px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; gap: 10px; }
.mini-icon { width: 32px; height: 32px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mini-label { font-size: 11px; color: #64748b; margin-bottom: 2px; }
.mini-value { font-size: 18px; font-weight: 600; color: #0f172a; }
.mini-arrow { color: #94a3b8; font-size: 12px; text-decoration: none; }
.quick-card { background: #fff; border: 0.5px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; flex: 1; }
.quick-title { font-size: 10.5px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 10px; }
.quick-btn { display: flex; align-items: center; gap: 8px; padding: 8px 11px; border-radius: 8px; font-size: 12.5px; font-weight: 600; text-decoration: none; margin-bottom: 7px; transition: opacity .15s; }
.quick-btn:last-child { margin-bottom: 0; }
.quick-btn:hover { opacity: .85; }

/* Bottom row */
.bottom-row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr); gap: 12px; }
.list-item { display: flex; align-items: center; gap: 11px; padding: 10px 16px; border-bottom: 1px solid #f8fafc; }
.list-item:last-child { border-bottom: none; }
.item-icon { width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.item-name { font-size: 13.5px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.item-sub { font-size: 12px; color: #94a3b8; margin-top: 1px; }
.item-stock { font-size: 14px; font-weight: 700; }
.item-time { font-size: 11.5px; color: #94a3b8; }
.badge { display: inline-block; font-size: 10.5px; font-weight: 500; padding: 2px 7px; border-radius: 4px; margin-top: 2px; }
.badge-red { background: #fef2f2; color: #dc2626; }
.badge-yellow { background: #fffbeb; color: #d97706; }
.tx-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mono { font-family: 'Courier New', monospace; }
.empty-state { padding: 40px 20px; text-align: center; color: #94a3b8; font-size: 13.5px; }
</style>

<div class="dash">

    {{-- KPI Row --}}
    <div class="kpi-row">
        <div class="kpi">
            <div class="kpi-icon" style="background:#eff6ff;">
                <i class="fas fa-boxes-stacked" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="kpi-label">Total Produk</div>
                <div class="kpi-value">{{ number_format($stats['total_produk']) }}</div>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-icon" style="background:#fef2f2;">
                <i class="fas fa-triangle-exclamation" style="color:#dc2626;"></i>
            </div>
            <div>
                <div class="kpi-label">Stok Kritis</div>
                <div class="kpi-value" style="color:#dc2626;">{{ $stats['stok_menipis'] + $stats['stok_habis'] }}</div>
                <div class="kpi-sub">{{ $stats['stok_habis'] }} habis · {{ $stats['stok_menipis'] }} menipis</div>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-icon" style="background:#fffbeb;">
                <i class="fas fa-arrows-rotate" style="color:#d97706;"></i>
            </div>
            <div>
                <div class="kpi-label">Transaksi Hari Ini</div>
                <div class="kpi-value">{{ $stats['transaksi_hari_ini'] }}</div>
            </div>
        </div>
        <div class="kpi">
    <div class="kpi-icon" style="background:#ecfeff;">
        <i class="fas fa-trophy" style="color:#0891b2;"></i>
    </div>
    <div>
        <div class="kpi-label">Stok Terbanyak</div>
        <div class="kpi-value">
            {{ number_format($stats['stok_terbanyak']) }}
        </div>
        <div class="kpi-sub">
            {{ $stats['produk_terbanyak'] }}
        </div>
    </div>
</div>
    </div>

    {{-- Chart + Side --}}
    <div class="mid-row">
        <div class="side-col">
            <div class="mini-stat">
                <div class="mini-icon" style="background:#f3f4f6;">
                    <i class="fas fa-tags" style="color:#6366f1; font-size:13px;"></i>
                </div>
                <div style="flex:1;">
                    <div class="mini-label">Kategori</div>
                    <div class="mini-value">{{ $stats['total_kategori'] }}</div>
                </div>
                <a href="{{ route('kategori.index') }}" class="mini-arrow"><i class="fas fa-chevron-right"></i></a>
            </div>

            <div class="mini-stat">
                <div class="mini-icon" style="background:#fff7ed;">
                    <i class="fas fa-truck" style="color:#ea580c; font-size:13px;"></i>
                </div>
                <div style="flex:1;">
                    <div class="mini-label">Supplier</div>
                    <div class="mini-value">{{ $stats['total_supplier'] }}</div>
                </div>
                <a href="{{ route('supplier.index') }}" class="mini-arrow"><i class="fas fa-chevron-right"></i></a>
            </div>

        </div>
        <div class="quick-card">
            <div class="quick-title">Aksi Cepat</div>
            <a href="{{ route('transaksi.create') }}" class="quick-btn" style="background:#f0fdf4; color:#16a34a;">
                <i class="fas fa-arrow-down fa-xs"></i> Barang Masuk
            </a>
            <a href="{{ route('transaksi.create') }}" class="quick-btn" style="background:#fef2f2; color:#dc2626;">
                <i class="fas fa-arrow-up fa-xs"></i> Barang Keluar
            </a>
            <a href="{{ route('laporan.index') }}" class="quick-btn" style="background:#eff6ff; color:#2563eb;">
                <i class="fas fa-chart-bar fa-xs"></i> Lihat Laporan
            </a>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="bottom-row">

        {{-- Stok Kritis --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Stok Kritis</span> 
                <a href="{{ route('inventory.index') }}?status=menipis" class="card-link">Lihat semua</a>
            </div>
            @if($produkKritis->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-check-circle" style="font-size:24px; display:block; margin-bottom:8px; color:#bbf7d0;"></i>
                    Semua stok aman
                </div>
            @else
                @foreach($produkKritis as $p)
                <div class="list-item">
                    <div class="item-icon">
                        <i class="fas fa-box" style="font-size:13px; color:#94a3b8;"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div class="item-name">{{ $p->nama }}</div>
                        <div class="item-sub">{{ $p->kategori->nama }}</div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div class="item-stock" style="color:{{ $p->status === 'habis' ? '#dc2626' : '#d97706' }};">
                            {{ $p->stok }} <span style="font-size:11px; font-weight:400; color:#94a3b8;">{{ $p->satuan }}</span>
                        </div>
                        @if($p->status === 'habis')
                            <span class="badge badge-red">Stok Habis</span>
                        @else
                            <span class="badge badge-yellow">Stok Menipis</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
 {{-- Stok terbanyak --}}
        <div class="card">
    <div class="card-header">
        <span class="card-title">Top 5 Stok Terbanyak</span>
    </div>

    @foreach($stokTerbanyak as $produk)
    <div class="list-item">
        <div class="item-icon">
            <i class="fas fa-cubes"
               style="font-size:13px;color:#3b82f6;"></i>
        </div>

        <div style="flex:1;min-width:0;">
            <div class="item-name">{{ $produk->nama }}</div>
            <div class="item-sub">
                {{ $produk->kategori?->nama }}
            </div>
        </div>

        <div style="text-align:right;">
            <div class="item-stock" style="color:#2563eb;">
                {{ number_format($produk->stok) }}
            </div>
            <div class="item-time">
                {{ $produk->satuan }}
            </div>
        </div>
    </div>
    @endforeach
</div>
        {{-- Transaksi Terakhir --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Aktivitas Transaksi</span>
                <a href="{{ route('transaksi.index') }}" class="card-link">Lihat semua</a>
            </div>
            @if($transaksiTerakhir->isEmpty())
                <div class="empty-state">Belum ada transaksi</div>
            @else
                @foreach($transaksiTerakhir as $t)
<div class="list-item">
    <div class="tx-icon" style="background:{{ $t->tipe === 'masuk' ? '#dcfce7' : '#fee2e2' }}; color:{{ $t->tipe === 'masuk' ? '#16a34a' : '#dc2626' }};">
        <i class="fas fa-{{ $t->tipe === 'masuk' ? 'arrow-down' : 'arrow-up' }}" style="font-size:11px;"></i>
    </div>

    <div style="flex:1; min-width:0;">
        <div class="item-name">{{ $t->produk?->nama ?? 'Produk dihapus' }}</div>
        <div class="item-sub mono">{{ $t->nomor_referensi }}</div>
    </div>

    <div style="text-align:right; flex-shrink:0;">
        <div style="font-size:13.5px; font-weight:700; color:{{ $t->tipe === 'masuk' ? '#16a34a' : '#dc2626' }};">
            {{ $t->tipe === 'masuk' ? '+' : '-' }}{{ number_format($t->jumlah) }}
        </div>
        <div class="item-time">{{ $t->tanggal_transaksi->diffForHumans() }}</div>
    </div>
</div>
@endforeach
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const raw = @json($chartData);
const days = [];
for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    days.push(d.toISOString().split('T')[0]);
}
const labels = days.map(d => { const p = d.split('-'); return p[2] + '/' + p[1]; });
const masuk  = days.map(d => raw.find(r => r.tgl === d && r.tipe === 'masuk')?.total  || 0);
const keluar = days.map(d => raw.find(r => r.tgl === d && r.tipe === 'keluar')?.total || 0);

new Chart(document.getElementById('dashChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Masuk',  data: masuk,  backgroundColor: '#86efac', borderRadius: 4, borderSkipped: false },
            { label: 'Keluar', data: keluar, backgroundColor: '#fca5a5', borderRadius: 4, borderSkipped: false },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
                labels: { font: { size: 12 }, boxWidth: 10, padding: 12 }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush
@endsection