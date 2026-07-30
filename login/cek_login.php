<?php
session_start();
include '../config/koneksi.php';

// --- LOGIKA LOGIN SISWA ---
if(isset($_POST['login_siswa'])) {
    $nis = $_POST['nis'];

    // Cek apakah NIS ada di database siswa
    $query = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
    
    if(mysqli_num_rows($query) > 0) {
        // Jika ada, buat session siswa
        $_SESSION['status'] = 'login_siswa';
        $_SESSION['nis'] = $nis;
        header("Location: ../index.php"); // Arahkan ke halaman form aspirasi
    } else {
        echo "<script>alert('NIS tidak terdaftar!'); window.location='login.php';</script>";
    }
}

// --- LOGIKA LOGIN ADMIN ---
elseif(isset($_POST['login_admin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username dan password admin
    $query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    
    if(mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        // Jika benar, buat session admin
        $_SESSION['status'] = 'login_admin';
        $_SESSION['id_admin'] = $data['id_admin'];
        $_SESSION['username'] = $data['username'];
        header("Location: ../admin/admin.php"); // Arahkan ke halaman admin
    } else {
        echo "<script>alert('Username atau Password Admin salah!'); window.location='login.php';</script>";
    }
}
?>