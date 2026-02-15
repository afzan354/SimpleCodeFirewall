<?php
// koneksi database
$conn = mysqli_connect("localhost", "root", "", "belajar_mysql");

// ambil semua data buku
$query = mysqli_query($conn, "SELECT * FROM books");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Daftar Buku</title>
</head>

<body>

    <h2>Daftar Buku</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Tahun</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['published_year']; ?></td>
                <td><?php echo $row['stock']; ?></td>
                <td>
                    <a href="detail.php?id=<?php echo $row['id']; ?>">
                        Lihat Detail
                    </a>
                </td>
            </tr>
        <?php } ?>

    </table>

</body>

</html>