<?php
include '../config/koneksi.php';

// Ambil ID dan pastikan angka (integer) untuk keamanan
$id_pelaporan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Ambil data laporan asli (untuk ditampilkan infonya)
$q = mysqli_query($conn, "SELECT * FROM input_aspirasi WHERE id_pelaporan = '$id_pelaporan'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

// 2. Ambil data status yang sudah ada (jika ada) di tabel aspirasi
$q_status = mysqli_query($conn, "SELECT * FROM aspirasi WHERE id_pelaporan = '$id_pelaporan'");
$data_status = mysqli_fetch_assoc($q_status);

// Tentukan nilai default jika belum ada data (Null Coalescing)
$current_status = $data_status['status'] ?? 'Menunggu';
$current_feedback = $data_status['feedback'] ?? '';

// 3. Proses Update saat tombol ditekan
if (isset($_POST['update'])) {
    $status_baru = $_POST['status'];
    $feedback_baru = $_POST['feedback'];
    
    // Ambil id_kategori dari data laporan asli
    $id_kategori = $data['id_kategori']; 

    if ($data_status) {
        // Jika sudah ada di tabel aspirasi, lakukan UPDATE
        // Gunakan Prepared Statement agar aman dari petik di feedback
        $stmt = $conn->prepare("UPDATE aspirasi SET status=?, feedback=? WHERE id_pelaporan=?");
        $stmt->bind_param("ssi", $status_baru, $feedback_baru, $id_pelaporan);
        $exec = $stmt->execute();
    } else {
        // Jika belum ada, lakukan INSERT
        $stmt = $conn->prepare("INSERT INTO aspirasi (id_pelaporan, status, id_kategori, feedback) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $id_pelaporan, $status_baru, $id_kategori, $feedback_baru);
        $exec = $stmt->execute();
    }

    if ($exec) {
        header("Location: admin.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Status</title>
    <link rel="stylesheet" href="update_status.css"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h3>Update Status Laporan #<?= $id_pelaporan; ?></h3>
        
        <div class="info-box">
            <div class="info-item">
                <span class="label">Lokasi</span>
                <span class="value"><?= htmlspecialchars($data['lokasi']); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Keluhan</span>
                <span class="value"><?= htmlspecialchars($data['ket']); ?></span>
            </div>
        </div>

        <form method="POST">
            <label>Status Penyelesaian:</label>
            <select name="status">
                <option value="Menunggu" <?= $current_status == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="Proses" <?= $current_status == 'Proses' ? 'selected' : '' ?>>Proses</option>
                <option value="Selesai" <?= $current_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
            
            <br><br>

            <label>Umpan Balik / Feedback:</label>
            <textarea name="feedback" rows="4"><?= htmlspecialchars($current_feedback); ?></textarea>

            <br><br>
            
            <button type="submit" name="update">Simpan Perubahan</button>
            <a href="admin.php">Batal</a>
        </form>
    </div>
</body>
</html>