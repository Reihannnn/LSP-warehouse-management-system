<?php
require "../db.php";

$query = "
SELECT 
s.*,
IFNULL(AVG(r.rating),0) as rata_rating,
COUNT(r.id_rating) as total_rating,
IFNULL(SUM(p.jumlah),0) as total_terjual
FROM users s
LEFT JOIN rating_sales r ON s.id = r.id_sales
LEFT JOIN penjualan p ON s.id = p.id_sales
GROUP BY s.id
ORDER BY rata_rating DESC
";

$result = mysqli_query($db, $query);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tim Sales Kami</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #eef2ff, #f8f9ff);
}

.sales-card{
    border-radius:20px;
    transition:0.3s;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.sales-card:hover{
    transform:translateY(-8px);
    box-shadow:0 20px 40px rgba(0,0,0,0.1);
}

.profile-icon{
    width:70px;
    height:70px;
    background:#e7efff;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    color:#2a6edb;
}

.star{
    font-size:22px;
    color:#ccc;
    cursor:pointer;
}

.star.active{
    color:gold;
}

.review-box{
    background:#f8f9fc;
    border-radius:10px;
}
</style>
</head>

<body>

<?php include 'navbar_customer.php'; ?>

<div class="container py-5">

<h2 class="text-center fw-bold">Tim Sales Kami</h2>
<p class="text-center text-muted mb-5">
Hubungi sales profesional kami untuk mendapatkan penawaran terbaik
</p>

<div class="row g-4">

<?php while($data = mysqli_fetch_assoc($result)) { 

// Hitung lama kerja
$lama = "-";
if(!empty($data['tanggal_gabung'])){
    $tahun = date('Y') - date('Y', strtotime($data['tanggal_gabung']));
    $lama = $tahun . " Tahun";
}

// Ambil review
$id_sales = $data['id'];
$reviewQ = mysqli_query($db,"SELECT * FROM review_sales WHERE id_sales='$id_sales' ORDER BY tanggal DESC LIMIT 2");

?>

<div class="col-md-6">
<div class="card sales-card p-4">

<div class="d-flex align-items-center mb-3">

<div class="profile-icon me-3">
<i class="bi bi-person-fill"></i>
</div>

<div>
<h5 class="fw-bold mb-0"><?= $data['username'] ?></h5>
<small class="text-muted"><?= $data['email'] ?></small><br>
<small class="text-primary">Sales Consultant</small>

<div class="mt-1">
<span class="text-warning">
<?php
$rating = round($data['rata_rating']);
for($i=1;$i<=5;$i++){
echo $i <= $rating ? "★" : "☆";
}
?>
</span>
<small class="text-muted">
<?= number_format($data['rata_rating'],1) ?> (<?= $data['total_rating'] ?> reviews)
</small>
</div>

<div class="text-muted small mt-1">
📅 <?= $lama ?> • 🚀 <?= $data['total_terjual'] ?> unit terjual
</div>

</div>
</div>

<!-- Tombol -->
<div class="row g-2 mb-3">
<div class="col-6">
<a href="https://wa.me/<?= $data['no_telepon'] ?>" class="btn btn-success w-100">WhatsApp</a>
</div>
<div class="col-6">
<a href="tel:<?= $data['no_telepon'] ?>" class="btn btn-primary w-100">Telepon</a>
</div>
</div>

<hr>

<!-- Rating -->
<form action="proses_rating.php" method="POST">

<input type="hidden" name="id_sales" value="<?= $data['id'] ?>">
<input type="hidden" name="rating" class="rating-value">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

    <!-- Bintang -->
    <div>
        <?php for($i=1;$i<=5;$i++){ ?>
        <span class="star" onclick="setRating(this,<?= $i ?>)">★</span>
        <?php } ?>
    </div>

    <!-- Tombol -->
    <button type="button" 
    class="btn btn-outline-primary btn-sm"
    onclick="bukaModal(<?= $data['id'] ?>,'<?= $data['username'] ?>')">
    Tulis Review
    </button>

</div>

</form>

<hr>

<!-- MODAL REVIEW -->
<div class="modal fade" id="modalReview" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">

      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">
          Tulis Review untuk <span id="namaSales"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="proses_review.php" method="POST">

        <input type="hidden" name="id_sales" id="idSales">
        <input type="hidden" name="rating" id="ratingValue">

        <p>👤 <b><?= $_SESSION['nama_user'] ?? 'Guest' ?></b></p>

        <div class="mb-2">
          <span class="star" onclick="pilihBintang(1)">★</span>
          <span class="star" onclick="pilihBintang(2)">★</span>
          <span class="star" onclick="pilihBintang(3)">★</span>
          <span class="star" onclick="pilihBintang(4)">★</span>
          <span class="star" onclick="pilihBintang(5)">★</span>
        </div>

        <textarea name="komentar" class="form-control" placeholder="Tulis review..." required></textarea>

        <button class="btn btn-primary w-100 mt-3">Kirim Review</button>

      </form>

    </div>
  </div>
</div>

<!-- Review Asli -->
<?php while($rev = mysqli_fetch_assoc($reviewQ)) { ?>
<div class="review-box p-3 mb-2">
<strong><?= $rev['nama_customer'] ?></strong>
<p class="small mb-1"><?= $rev['komentar'] ?></p>
<span class="text-warning">
<?php for($i=1;$i<=5;$i++){
echo $i <= $rev['rating'] ? "★" : "☆";
} ?>
</span>
</div>
<?php } ?>

</div>
</div>

<?php } ?>

</div>
</div>

<script>
function setRating(element, rating) {
let stars = element.parentNode.querySelectorAll(".star");
stars.forEach((star, index) => {
if(index < rating){
star.classList.add("active");
}else{
star.classList.remove("active");
}
});
element.closest("form").querySelector(".rating-value").value = rating;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function bukaModal(id, nama){
    document.getElementById("idSales").value = id;
    document.getElementById("namaSales").innerText = nama;

    new bootstrap.Modal(document.getElementById('modalReview')).show();
}

function pilihBintang(rating){
    document.getElementById("ratingValue").value = rating;

    let stars = document.querySelectorAll("#modalReview .star");

    stars.forEach((star, index)=>{
        if(index < rating){
            star.classList.add("active");
        }else{
            star.classList.remove("active");
        }
    });
}
</script>

</body>
</html>