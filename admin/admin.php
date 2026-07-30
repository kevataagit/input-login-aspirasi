<?php
session_start();
include '../config/koneksi.php';

// ===============================
// CEK KEAMANAN
// ===============================
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login_admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../login/login.php';</script>";
    exit();
}

// ===============================
// FILTER
// ===============================
$where_clause = "WHERE 1=1";

// STATUS
$status_filter = $_GET['status'] ?? '';
if ($status_filter != '') {
    $status = mysqli_real_escape_string($conn, $status_filter);
    if ($status == 'Menunggu') {
        $where_clause .= " AND (a.status = 'Menunggu' OR a.status IS NULL)";
    } else {
        $where_clause .= " AND a.status = '$status'";
    }
}

// TANGGAL
if (!empty($_GET['tanggal'])) {
    $tanggal = mysqli_real_escape_string($conn, $_GET['tanggal']);
    $where_clause .= " AND DATE(i.tgl_lapor) = '$tanggal'";
}

// BULAN
if (!empty($_GET['bulan'])) {
    $bulan = mysqli_real_escape_string($conn, $_GET['bulan']);
    $where_clause .= " AND DATE_FORMAT(i.tgl_lapor, '%Y-%m') = '$bulan'";
}

// SISWA
if (!empty($_GET['nis'])) {
    $nis = mysqli_real_escape_string($conn, $_GET['nis']);
    $where_clause .= " AND i.nis = '$nis'";
}

// KATEGORI
if (!empty($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
    $where_clause .= " AND i.id_kategori = '$kategori'";
}

// ===============================
// PAGINATION
// ===============================
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// HITUNG TOTAL DATA
$query_count = "
    SELECT COUNT(*) AS total
    FROM input_aspirasi i
    LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan
    $where_clause
";
$total_data = mysqli_fetch_assoc(mysqli_query($conn, $query_count))['total'];
$total_page = ceil($total_data / $limit);

// ===============================
// QUERY DATA
// ===============================
$query = "
    SELECT
        i.id_pelaporan,
        i.nis,
        k.ket_kategori,
        i.lokasi,
        i.ket,
        i.tgl_lapor,
        a.status,
        a.feedback
    FROM input_aspirasi i
    LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
    LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan
    $where_clause
    ORDER BY i.tgl_lapor DESC
    LIMIT $limit OFFSET $offset
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin - Aspirasi</title>
    <link rel="stylesheet" href="admin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="container">
        <h2>
            Selamat datang,
            <?= htmlspecialchars($_SESSION['username']); ?>
        </h2>

        <h2>Kelola Aspirasi</h2>
        <button class="btn-logout" onclick="location.href='../login/logout.php'">Logout</button>

        <!-- STATUS FILTER -->
        <div class="status-filter">
            <a href="admin.php" class="<?= $status_filter == '' ? 'active' : '' ?>">Semua</a>
            <a href="?status=Menunggu" class="<?= $status_filter == 'Menunggu' ? 'active' : '' ?>">Menunggu</a>
            <a href="?status=Proses" class="<?= $status_filter == 'Proses' ? 'active' : '' ?>">Proses</a>
            <a href="?status=Selesai" class="<?= $status_filter == 'Selesai' ? 'active' : '' ?>">Selesai</a>
        </div>

        <!-- FILTER LANJUTAN -->
        <form method="GET" class="filter-box">
            <input type="date" name="tanggal" value="<?= $_GET['tanggal'] ?? '' ?>">
            <input type="month" name="bulan" value="<?= $_GET['bulan'] ?? '' ?>">
            <input type="text" name="nis" placeholder="NIS" value="<?= $_GET['nis'] ?? '' ?>">

            <select name="kategori">
                <option value="" >Semua Kategori</option>
                <?php
                $kat = mysqli_query($conn, "SELECT * FROM kategori");
                while ($k = mysqli_fetch_assoc($kat)) {
                    $sel = (($_GET['kategori'] ?? '') == $k['id_kategori']) ? 'selected' : '';
                    echo "<option value='{$k['id_kategori']}' $sel>{$k['ket_kategori']}</option>";
                }
                ?>
            </select>

            <button type="submit" class="terap">Terapkan</button>
        </form>

        <!-- TABLE -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Feedback</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = $offset + 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td data-label="No"><?= $no++ ?></td>
                        <td data-label="NIS"><?= htmlspecialchars($row['nis']) ?></td>
                        <td data-label="Kategori"><?= htmlspecialchars($row['ket_kategori']) ?></td>
                        <td data-label="Lokasi"><?= htmlspecialchars($row['lokasi']) ?></td>
                        <td data-label="Keterangan"><?= htmlspecialchars($row['ket']) ?></td>
                        <td data-label="Status"><?= htmlspecialchars($row['status'] ?? 'Menunggu') ?></td>
                        <td data-label="Feedback"><?= htmlspecialchars($row['feedback'] ?? '-') ?></td>
                        <td data-label="Aksi">
                            <div class="actions">
                                <a href="update_status.php?id=<?= $row['id_pelaporan'] ?>" class="btn-edit">Proses</a>
                                <a href="hapus.php?id=<?= $row['id_pelaporan'] ?>" class="btn-hapus"
                                    onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_page; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                    class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor ?>
        </div>
    </div>

</body>

</html>