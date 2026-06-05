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

.sales-card {
    border-radius: 20px;
    transition: 0.3s;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.sales-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.profile-icon {
    width: 70px;
    height: 70px;
    background: #e7efff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: #2a6edb;
    flex-shrink: 0;
}

.star {
    font-size: 22px;
    color: #ccc;
    cursor: pointer;
    transition: color 0.2s;
}

.star.active {
    color: gold;
}

.star:hover {
    color: gold;
}

.review-box {
    background: #f8f9fc;
    border-radius: 10px;
}

/* Fix z-index modal agar tidak tertindih */
.modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1050 !important;
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

        // Ambil review (2 terbaru)
        $id_sales = $data['id'];
        $reviewQ = mysqli_query($db, "SELECT * FROM review_sales WHERE id_sales='$id_sales' ORDER BY tanggal DESC LIMIT 2");

    ?>

        <div class="col-md-6">
        <div class="card sales-card p-4">

            <!-- Header: Avatar + Info -->
            <div class="d-flex align-items-center mb-3">
                <div class="profile-icon me-3">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($data['username']) ?></h5>
                    <small class="text-muted"><?= htmlspecialchars($data['email']) ?></small><br>
                    <small class="text-primary">Sales Consultant</small>

                    <!-- Rating bintang -->
                    <div class="mt-1">
                        <span class="text-warning">
                        <?php
                        $rating = round($data['rata_rating']);
                        for($i = 1; $i <= 5; $i++){
                            echo $i <= $rating ? "★" : "☆";
                        }
                        ?>
                        </span>
                        <small class="text-muted">
                            <?= number_format($data['rata_rating'], 1) ?> (<?= $data['total_rating'] ?> reviews)
                        </small>
                    </div>

                    <div class="text-muted small mt-1">
                        📅 <?= $lama ?> &bull; 🚀 <?= $data['total_terjual'] ?> unit terjual
                    </div>
                </div>
            </div>

            <!-- Tombol WhatsApp & Telepon -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <a href="https://wa.me/<?= htmlspecialchars($data['no_telepon']) ?>" 
                       target="_blank"
                       class="btn btn-success w-100">
                       <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                </div>
                <div class="col-6">
                    <a href="tel:<?= htmlspecialchars($data['no_telepon']) ?>" 
                       class="btn btn-primary w-100">
                       <i class="bi bi-telephone"></i> Telepon
                    </a>
                </div>
            </div>

            <hr>

            <!-- Tombol Buka Modal Review -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <small class="text-muted">Beri penilaian untuk sales ini:</small>
                <button type="button"
                    class="btn btn-outline-primary btn-sm"
                    onclick="bukaModal(<?= $data['id'] ?>, '<?= htmlspecialchars($data['username'], ENT_QUOTES) ?>')">
                    <i class="bi bi-pencil-square"></i> Tulis Review
                </button>
            </div>

            <hr>

            <!-- Review Terbaru -->
            <?php if(mysqli_num_rows($reviewQ) > 0) { ?>
                <p class="text-muted small mb-2"><strong>Review Terbaru:</strong></p>
                <?php while($rev = mysqli_fetch_assoc($reviewQ)) { ?>
                <div class="review-box p-3 mb-2">
                    <strong><?= htmlspecialchars($rev['nama_customer']) ?></strong>
                    <p class="small mb-1"><?= htmlspecialchars($rev['komentar']) ?></p>
                    <span class="text-warning">
                    <?php for($i = 1; $i <= 5; $i++){
                        echo $i <= $rev['rating'] ? "★" : "☆";
                    } ?>
                    </span>
                </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-muted small">Belum ada review untuk sales ini.</p>
            <?php } ?>

        </div>
        </div>

    <?php } // END while ?>

    </div><!-- end row -->
</div><!-- end container -->


<!-- =============================================
     MODAL REVIEW — Di luar loop, SATU modal saja
     ============================================= -->
<div class="modal fade" id="modalReview" tabindex="-1" aria-labelledby="modalReviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">

      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="modalReviewLabel">
          Tulis Review untuk <span id="namaSales" class="text-primary"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form action="proses_review.php" method="POST" id="formReview">

          <input type="hidden" name="id_sales" id="idSales">
          <input type="hidden" name="rating" id="ratingValue" value="0">

          <p class="mb-3">👤 <b><?= htmlspecialchars($_SESSION['nama_user'] ?? 'Guest') ?></b></p>

          <!-- Bintang Pilihan -->
          <div class="mb-3">
            <label class="form-label text-muted small">Pilih Rating:</label><br>
            <span class="star modal-star" onclick="pilihBintang(1)">★</span>
            <span class="star modal-star" onclick="pilihBintang(2)">★</span>
            <span class="star modal-star" onclick="pilihBintang(3)">★</span>
            <span class="star modal-star" onclick="pilihBintang(4)">★</span>
            <span class="star modal-star" onclick="pilihBintang(5)">★</span>
            <small class="text-danger ms-2" id="ratingError" style="display:none;">Pilih rating dulu!</small>
          </div>

          <!-- Komentar -->
          <div class="mb-3">
            <label class="form-label text-muted small">Komentar:</label>
            <textarea name="komentar" id="komentarReview" class="form-control" rows="3" 
                      placeholder="Tulis pengalaman Anda dengan sales ini..." required></textarea>
          </div>

          <button type="submit" class="btn btn-primary w-100" onclick="return validasiRating()">
            <i class="bi bi-send"></i> Kirim Review
          </button>

        </form>
      </div>

    </div>
  </div>
</div>
<!-- END MODAL -->


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ========================
// Buka modal & isi data
// ========================
function bukaModal(id, nama) {
    document.getElementById("idSales").value = id;
    document.getElementById("namaSales").innerText = nama;

    // Reset bintang & form setiap buka modal
    document.getElementById("ratingValue").value = 0;
    document.getElementById("komentarReview").value = "";
    document.getElementById("ratingError").style.display = "none";

    let stars = document.querySelectorAll(".modal-star");
    stars.forEach(star => star.classList.remove("active"));

    let modal = new bootstrap.Modal(document.getElementById("modalReview"));
    modal.show();
}

// ========================
// Pilih bintang di modal
// ========================
function pilihBintang(rating) {
    document.getElementById("ratingValue").value = rating;
    document.getElementById("ratingError").style.display = "none";

    let stars = document.querySelectorAll(".modal-star");
    stars.forEach((star, index) => {
        if(index < rating){
            star.classList.add("active");
        } else {
            star.classList.remove("active");
        }
    });
}

// ========================
// Validasi sebelum submit
// ========================
function validasiRating() {
    let rating = document.getElementById("ratingValue").value;
    if(rating == 0 || rating == "") {
        document.getElementById("ratingError").style.display = "inline";
        return false;
    }
    return true;
}

// ========================
// Hover efek bintang modal
// ========================
document.addEventListener("DOMContentLoaded", function(){
    let stars = document.querySelectorAll(".modal-star");
    stars.forEach((star, i) => {
        star.addEventListener("mouseover", function(){
            stars.forEach((s, j) => {
                if(j <= i) s.style.color = "gold";
                else s.style.color = "#ccc";
            });
        });
        star.addEventListener("mouseout", function(){
            let currentRating = parseInt(document.getElementById("ratingValue").value) || 0;
            stars.forEach((s, j) => {
                if(j < currentRating) s.style.color = "gold";
                else s.style.color = "#ccc";
            });
        });
    });
});
</script>

</body>
</html>