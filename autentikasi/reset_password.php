<?php
include '../config/conn_db.php';

$message = "";

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $query = "SELECT * FROM users WHERE reset_token='$token'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        die("Token tidak valid atau sudah digunakan.");
    }
} else {
    die("Token tidak ditemukan.");
}

if (isset($_POST['change'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $message = "Password minimal 6 karakter!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET password='$hashed', reset_token=NULL WHERE reset_token='$token'";
        if (mysqli_query($conn, $update)) {
            $message = "<span style='color:green;'>Password berhasil diubah. <a href='login.php'>Login sekarang</a>.</span>";
        } else {
            $message = "Gagal memperbarui passwordmu..";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>

    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            width: 380px;
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 16px;
            box-shadow: 0 12px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #0d47a1;
            font-size: 24px;
            font-weight: 700;
        }

        label {
            font-size: 14px;
            color: #444;
            font-weight: 600;
        }

        input[type=password] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 18px 0;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.2s;
        }

        input:focus {
            border-color: #1e88e5;
            box-shadow: 0 0 5px rgba(30,136,229,0.4);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(25,118,210,0.3);
        }

        button:hover {
            background: #0d47a1;
            box-shadow: 0 6px 15px rgba(13,71,161,0.35);
        }

        .msg {
            margin-top: 15px;
            color: #c62828;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

</head>
<body>

    <div class="form-container">
        <h3>Reset Password</h3>

        <form method="POST">
            <label>Password Baru:</label>
            <input type="password" name="password" minlength="6" required>

            <label>Konfirmasi Password:</label>
            <input type="password" name="confirm_password" minlength="6" required>

            <button type="submit" name="change">Ubah Password</button>
        </form>

        <div class="msg"><?= $message ?></div>
    </div>

</body>
</html>
