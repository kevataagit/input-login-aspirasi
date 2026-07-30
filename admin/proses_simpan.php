<?php
include '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $nis = $_POST['nis'];
    $id_kategori = $_POST['id_kategori'];
    $lokasi = $_POST['lokasi'];
    $ket = $_POST['ket'];

    // 1. Cek apakah NIS ada di database siswa (Validasi sederhana)
    // Gunakan escape string untuk NIS juga demi keamanan
    $safe_nis = mysqli_real_escape_string($conn, $nis);
    $cek_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$safe_nis'");

    if (mysqli_num_rows($cek_siswa) > 0) {
        
        // 2. Simpan ke tabel input_aspirasi menggunakan PREPARED STATEMENT (Lebih Aman)
        $stmt = $conn->prepare("INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket) VALUES (?, ?, ?, ?)");
        
        // "siss" -> string, integer, string, string (sesuaikan tipe data di db)
        // Jika id_kategori di database tipe INT, gunakan 'i', jika VARCHAR gunakan 's'
        $stmt->bind_param("siss", $nis, $id_kategori, $lokasi, $ket);

        if ($stmt->execute()) {
            echo "<script>alert('Laporan Berhasil Disimpan!'); window.location='../index.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();

    } else {
        echo "<script>alert('NIS tidak ditemukan! Harap daftar dulu.'); window.location='../index.php';</script>";
    }
}
?>