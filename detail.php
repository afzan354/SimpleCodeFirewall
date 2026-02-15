<?php
// koneksi database
include 'rate_limit.php';
$conn = mysqli_connect("localhost", "root", "", "belajar_mysql");

// ambil id dari URL
$id = $_GET['id'];

// query detail buku
$query = mysqli_query($conn, "SELECT * FROM books WHERE id = $id");
$data = mysqli_fetch_assoc($query);


?>


<!DOCTYPE html>
<html>

<head>
    <title>Detail Buku</title>
</head>

<body>

    <h2>Detail Buku</h2>

    <p><strong>ID:</strong> <?php echo $data['id']; ?></p>
    <p><strong>Judul:</strong> <?php echo $data['title']; ?></p>
    <p><strong>Kategori:</strong> <?php echo $data['category']; ?></p>
    <p><strong>ISBN:</strong> <?php echo $data['isbn']; ?></p>
    <p><strong>Tahun Terbit:</strong> <?php echo $data['published_year']; ?></p>
    <p><strong>Stok:</strong> <?php echo $data['stock']; ?></p>
    <p><strong>Dibuat Pada:</strong> <?php echo $data['created_at']; ?></p>

    <br>
    <a href="index.php">Kembali ke Daftar Buku</a>

</body>

</html>