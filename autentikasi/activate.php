<?php
require '../config/conn_db.php';

$success = false;   
$message = "";      

if (!isset($_GET['token'])) {
    $message = "Token tidak ditemukan!";
} else {

    $token = $_GET['token'];

    $sql = "SELECT * FROM users WHERE aktivasi='$token'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $message = "Token tidak valid!";
    } else {

        $row = $result->fetch_assoc();
        if (strtotime($row['kadaluarsa']) < time()) {
            $message = "Token sudah kadaluarsa!";
        } else {
            $conn->query("
                UPDATE users 
                SET status_akun=1, aktivasi=NULL, kadaluarsa=NULL 
                WHERE id={$row['id']}
            ");

            $success = true;
            $message = "Akun berhasil diaktivasi! Silakan login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aktivasi Akun</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 420px;
            margin: 100px auto;
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 16px;
            box-shadow: 0 12px 25px rgba(0,0,0,0.1);
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        h2 {
            margin-bottom: 20px;
            color: #0d47a1;
            font-size: 26px;
        }

        .success {
            color: #2e7d32;
            font-size: 18px;
            font-weight: 600;
        }

        .error {
            color: #c62828;
            font-size: 18px;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 24px;
            font-size: 16px;
            background: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(25,118,210,0.3);
        }

        .btn:hover {
            background: #0d47a1;
            box-shadow: 0 6px 14px rgba(13,71,161,0.35);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Aktivasi Akun</h2>

        <p class="<?= $success ? 'success' : 'error' ?>">
            <?= $message ?>
        </p>

        <?php if ($success): ?>
            <a href="login.php" class="btn">Login Sekarang</a>
        <?php else: ?>
            <a href="register.php" class="btn">Kembali ke Registrasi</a>
        <?php endif; ?>
    </div>

</body>
</html>
