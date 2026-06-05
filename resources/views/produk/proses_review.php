<?php
session_start();
require "../db.php";

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_sales  = (int) $_POST['id_sales'];
    $rating    = (int) $_POST['rating'];
    $komentar  = mysqli_real_escape_string($db, trim($_POST['komentar']));
    $id_user   = (int) $_SESSION['id_user'];

    // Validasi rating
    if ($rating < 1 || $rating > 5) {
        echo "<script>alert('Rating tidak valid!'); history.back();</script>";
        exit;
    }

    // Validasi komentar
    if (empty($komentar)) {
        echo "<script>alert('Komentar tidak boleh kosong!'); history.back();</script>";
        exit;
    }

    // Cek apakah user sudah pernah review sales ini
    $cek = mysqli_query($db, "SELECT id_rating FROM rating_sales 
                              WHERE id_sales = '$id_sales' AND id_user = '$id_user'");

    if (mysqli_num_rows($cek) > 0) {
        // Update review yang sudah ada
        $sql = "UPDATE rating_sales 
                SET rating = '$rating', komentar = '$komentar', tanggal = CURRENT_TIMESTAMP
                WHERE id_sales = '$id_sales' AND id_user = '$id_user'";
    } else {
        // Insert review baru
        $sql = "INSERT INTO rating_sales (id_sales, id_user, rating, komentar) 
                VALUES ('$id_sales', '$id_user', '$rating', '$komentar')";
    }

    if (mysqli_query($db, $sql)) {
        echo "<script>alert('Review berhasil dikirim! Terima kasih.'); window.location.href = 'sales.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim review: " . mysqli_error($db) . "'); history.back();</script>";
    }

} else {
    header("Location: list_sales.php");
    exit;
}
?>