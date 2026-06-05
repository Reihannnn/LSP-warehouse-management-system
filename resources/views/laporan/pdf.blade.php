<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Laporan Inventory</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 3px 0;
        }

        .info {
            margin-bottom: 15px;
        }

        .info table {
            width: 100%;
        }

        .summary {
            margin-bottom: 20px;
        }

        .summary td {
            padding: 5px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: #f1f5f9;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        table.data td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>

    @php
        $totalMasuk = $transaksis->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $transaksis->where('tipe', 'keluar')->sum('jumlah');
        $nilaiMasuk = $transaksis->where('tipe', 'masuk')->sum('total_harga');
        $nilaiKeluar = $transaksis->where('tipe', 'keluar')->sum('total_harga');
    @endphp

    <div class="header">
        <h1>LAPORAN INVENTORY</h1>
        <p>Warehouse Inventory Management System</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="150"><strong>Periode</strong></td>
                <td>
                    :
                    {{ \Carbon\Carbon::parse($tglDari)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($tglSampai)->format('d M Y') }}
                </td>
            </tr>

            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ now()->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <table border="0">
            <tr>
                <td><strong>Total Barang Masuk</strong></td>
                <td>: {{ number_format($totalMasuk) }}</td>

                <td><strong>Total Barang Keluar</strong></td>
                <td>: {{ number_format($totalKeluar) }}</td>
            </tr>

            <tr>
                <td><strong>Nilai Barang Masuk</strong></td>
                <td>: Rp {{ number_format($nilaiMasuk, 0, ',', '.') }}</td>

                <td><strong>Nilai Barang Keluar</strong></td>
                <td>: Rp {{ number_format($nilaiKeluar, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Referensi</th>
                <th>Tipe</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse($transaksis as $i => $t)
                <tr>
                    <td>{{ $i + 1 }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d/m/Y') }}
                    </td>

                    <td>{{ $t->nomor_referensi }}</td>

                    <td>{{ strtoupper($t->tipe) }}</td>

                    <td>{{ $t->produk->nama }}</td>

                    <td>{{ $t->produk->kategori->nama ?? '-' }}</td>

                    <td class="text-right">
                        {{ number_format($t->jumlah) }}
                    </td>

                    <td>{{ $t->produk->satuan }}</td>

                    <td class="text-right">
                        Rp {{ number_format($t->harga_satuan, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($t->total_harga, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">
                        Tidak ada data transaksi
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <strong>Total Transaksi: {{ $transaksis->count() }}</strong>
    </div>

</body>
</html>