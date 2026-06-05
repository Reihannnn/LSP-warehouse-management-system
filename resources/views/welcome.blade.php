<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WareStock — Inventory Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet"  href="{{ asset('css/welcomePage.css') }}">
</head>
<body>

    <div class="card">
        <div class="icon">📦</div>
        <h1>WareStock</h1>
        <p>Sistem manajemen inventaris gudang. Pantau stok, kelola barang masuk &amp; keluar, dan buat laporan dengan mudah.</p>

        <div class="actions">
            <a href="{{ route('login') }}" class="btn btn-dark">Masuk ke Dasbor</a>
            <div class="divider"><hr><span>atau</span><hr></div>
            <a href="{{ route('register') }}" class="btn btn-outline">Buat Akun Baru</a>
        </div>
    </div>

    <footer>© {{ date('Y') }} WareStock · Warehouse Inventory Management</footer>

</body>
</html>