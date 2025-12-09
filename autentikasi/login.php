<?php
session_start();
include '../config/conn_db.php';

$message = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if ($user['status_akun'] == 0) {
            $message = "Akun belum diaktivasi. Silakan cek email mu.";
        } elseif (password_verify($password, $user['password'])) {

            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['nama_lengkap'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'] ?? 'user'; 

            header("Location: ../config/index.php");
            exit();

        } else {
            $message = "Password salah.";
        }

    } else {
        $message = "Email tidak terdaftar.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            margin: 0;
            height: 100vh;
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
            font-size: 26px;
            font-weight: 700;
        }

        label {
            font-size: 14px;
            color: #444;
            font-weight: 600;
        }

        input[type=email],
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
            box-shadow: 0 4px 10px rgba(25,118,210,0.3);
        }

        button:hover {
            background: #0d47a1;
            box-shadow: 0 6px 15px rgba(13,71,161,0.35);
        }

        .msg {
            margin-top: 10px;
            color: #c62828;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }

        .links {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
        }

        .links a {
            color: #0d47a1;
            text-decoration: none;
            font-weight: 600;
        }

        .links a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

</head>
<body>

    <div class="form-container">
        <h3>Login Event Organizer</h3>

        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Login</button>
        </form>

        <div class="msg"><?= $message ?></div>

        <div class="links">
            <a href="forgot_password.php">Lupa Password?</a> | 
            <a href="register.php">Belum punya akun?</a>
        </div>
    </div>

</body>
</html>
