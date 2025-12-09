<?php
require '../config/conn_db.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_lengkap = $_POST['nama'];  
    $email        = $_POST['email'];
    $password     = $_POST['password'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));
    $expired = date("Y-m-d H:i:s", strtotime("+1 day"));
    $cek = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($cek->num_rows > 0) {
        $message = "Email sudah digunakan!";
    } else {
        $sql = "INSERT INTO users 
                (email, password, aktivasi, kadaluarsa, status_akun, nama_lengkap) 
                VALUES 
                ('$email', '$hashed', '$token', '$expired', 0, '$nama_lengkap')";

        if ($conn->query($sql)) {

            // untuk kirim email aktivasi nya biar otomatis
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'violasalwapolnep@gmail.com';
                $mail->Password   = 'sxtu afry heph edgc'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('violasalwapolnep@gmail.com', 'Aktivasi Akun');
                $mail->addAddress($email);

                $mail->Subject = 'Aktivasi Akun Anda';
                $mail->Body    = "
                    Hai $nama_lengkap, <br><br>
                    Klik link ini untuk aktivasi akun Event Organizer mu!<br>
                    <a href='http://localhost/event_organizing/autentikasi/activate.php?token=$token'>
                        Aktivasi Sekarang Akunmu!
                    </a><br><br>
                    Link berlaku sampai: $expired
                ";
                $mail->isHTML(true);

                $mail->send();

                $message = "Pendaftaran berhasil! Silakan cek email untuk aktivasi.";

            } catch (Exception $e) {
                $message = "Gagal mengirim email: {$mail->ErrorInfo}";
            }

        } else {
            $message = "Gagal mendaftar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Event Organizer</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease;
        }

        h3 {
            text-align: center;
            color: #0d47a1;
            margin-bottom: 28px;
            font-size: 26px;
            font-weight: 700;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #444;
            font-weight: 600;
            font-size: 14px;
        }

        input[type=text],
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.2s;
        }

        input:focus {
            border-color: #1e88e5;
            box-shadow: 0 0 5px rgba(30, 136, 229, 0.4);
            outline: none;
        }

        .password-hint {
            font-size: 12px;
            color: #777;
            margin-top: -12px;
            margin-bottom: 15px;
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
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: #c62828;
        }

        .links {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
            color: #666;
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h3>Registrasi Event Organizer</h3>

        <form method="POST">
            <label>Nama Lengkap:</label>
            <input type="text" name="nama" value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>

            <label>Password:</label>
            <input type="password" name="password" minlength="6" required>
            <div class="password-hint">Minimal 6 karakter</div>

            <label>Konfirmasi Password:</label>
            <input type="password" name="confirm_password" minlength="6" required>

            <button type="submit" name="register">Daftar Sekarang!</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="msg"><?= $message ?></div>
        <?php endif; ?>

        <div class="links">
            Sudah punya akun? <a href="login.php">Login</a>
        </div>
    </div>

</body>

</html>
