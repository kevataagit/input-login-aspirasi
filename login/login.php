<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Aplikasi Pengaduan</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <div class="login-container">

        <!-- LOGIN SISWA -->
        <div id="form-siswa">
            <h2>Login Siswa</h2>
            <form action="cek_login.php" method="POST">
                <input type="number" name="nis" placeholder="Masukkan NIS" required>
                <button type="submit" name="login_siswa">Masuk</button>
            </form>
            <div class="toggle-btn">
                <button type="button" onclick="showAdmin()">Login sebagai Admin</button>
            </div>
        </div>

        <!-- LOGIN ADMIN -->
        <div id="form-admin" class="hidden">
            <h2>Login Admin</h2>
            <form action="cek_login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login_admin">Masuk Admin</button>
            </form>
            <div class="toggle-btn">
                <button type="button" onclick="showSiswa()">Login sebagai Siswa</button>
            </div>
        </div>

    </div>

    <script>
        function showAdmin() {
            document.getElementById('form-siswa').classList.add('hidden');
            document.getElementById('form-admin').classList.remove('hidden');
        }

        function showSiswa() {
            document.getElementById('form-admin').classList.add('hidden');
            document.getElementById('form-siswa').classList.remove('hidden');
        }
    </script>

</body>

</html>