<?php
session_start();
include '../config/koneksi.php';

// hanya admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login_admin') {
    die('Akses ditolak');
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin.php");
    exit;
}

$stmt = $conn->prepare(
    "DELETE FROM input_aspirasi WHERE id_pelaporan = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin.php");
exit;
