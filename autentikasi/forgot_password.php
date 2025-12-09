<?php
include '../config/conn_db.php';
include '../config/email_config.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if (isset($_POST['reset'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sql = "SELECT * FROM users WHERE email='$email' AND status_akun=1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {

        $token = bin2hex(random_bytes(16));
        $update = "UPDATE users SET reset_token='$token', reset_expired=DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                   WHERE email='$email'";
        
        if (mysqli_query($conn, $update)) {
            $link = "http://localhost/event_organizing/autentikasi/reset_password.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = 'tls';
                $mail->Port = SMTP_PORT;

                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Reset Password';
                $mail->Body = "
                    <h3>Halo!</h3>
                    <p>Kami menerima permintaan reset password.</p>
                    <p>Silakan klik link berikut untuk mengatur password baru:</p>
                    <a href='$link'>$link</a>
                    <br><br>
                    <p>Link ini hanya berlaku selama 1 jam.</p>
                ";

                $mail->send();
                $message = "<span style='color:green;'>Link reset password telah dikirim ke email mu!.</span>";
            } catch (Exception $e) {
                $message = "Gagal mengirim email. Error: {$mail->ErrorInfo}";
            }
        }

    } else {
        $message = "Email tidak ditemukan atau akun belum aktif.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>

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
            margin-bottom: 10px;
            color: #0d47a1;
            font-size: 24px;
        }

        p {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 0;
        }

        label {
            font-size: 14px;
            color: #444;
            font-weight: 600;
        }

        input[type=email] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 18px 0;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.2s;
        }

        input[type=email]:focus {
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
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
            font-weight: 600;
            color: #1976d2;
        }

        .links {
            margin-top: 18px;
            text-align: center;
        }

        .links a {
            color: #0d47a1;
            font-size: 14px;
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
        <h3>Lupa Password</h3>
        <p>Masukkan email untuk reset password</p>

        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            <button type="submit" name="reset">Kirim Link Reset</button>
        </form>

        <div class="msg"><?= $message ?></div>

        <div class="links">
            <a href="login.php">Kembali ke Login</a>
        </div>
    </div>

</body>
</html>

