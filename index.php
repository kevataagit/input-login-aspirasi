<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login_siswa') {
    header("Location: ../login/login.php");
    exit;
}

$nis_login = $_SESSION['nis'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h2>Form Aspirasi Siswa</h2>
    <p class="subtitle">Silakan laporkan keluhan sarana & prasarana sekolah</p>

    <form action="admin/proses_simpan.php" method="POST">

        <div class="form-group">
            <label>NIS Siswa</label>
            <input type="number" name="nis" value="<?= $nis_login ?>" readonly>
        </div>

        <div class="form-group">
            <label>Katerogi</label>
            <select name="id_kategori" required>
                <option value="">-- Pilih Katerogi --</option>
                <?php
                $kategori = mysqli_query($conn, "SELECT * FROM kategori");
                while ($k = mysqli_fetch_assoc($kategori)) {
                    echo "<option value='{$k['id_kategori']}'>{$k['ket_kategori']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi" placeholder="Ruang Guru" required>
        </div>

        <div class="form-group">
            <label>Keterangan Keluhan</label>
            <textarea name="ket" rows="3" placeholder="Jelaskan kerusakan..." required></textarea>
        </div>

        <button type="submit" name="simpan">Kirim Aspirasi</button>
    </form> 

    <hr>

    <h3>Histori & Status Aspirasi</h3>

    <!-- 🔥 WRAPPER BARU -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Feedback</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $conn->prepare("
                SELECT i.*, k.ket_kategori, a.status, a.feedback
                FROM input_aspirasi i
                LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
                LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan
                WHERE i.nis = ?
                ORDER BY i.tgl_lapor DESC
            ");
            $stmt->bind_param("s", $nis_login);
            $stmt->execute();
            $result = $stmt->get_result();

            $no = 1;
            while ($row = $result->fetch_assoc()) {
                $status = strtolower($row['status'] ?? 'menunggu');
                $feedback = $row['feedback'] ?? '-';

                echo "
                <tr>
                    <td>$no</td>
                    <td>{$row['tgl_lapor']}</td>
                    <td>{$row['ket_kategori']}</td>
                    <td>{$row['lokasi']}</td>
                    <td>{$row['ket']}</td>
                    <td class='status-$status'>$status</td>
                    <td>$feedback</td>
                </tr>";
                $no++;
            }
            ?>
            </tbody>
        </table>
    </div>

    <p class="footer">
        Halo, Siswa NIS: <b><?= $nis_login ?></b> |
        <a href="../login/logout.php" class="logout">Logout</a>
    </p>

</div>

</body>
</html>
