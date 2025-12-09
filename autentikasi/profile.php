<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../autentikasi/login.php");
    exit();
}

include '../config/conn_db.php';

$message = "";

if (isset($_POST['update'])) {
    $user_id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    
    $sql = "UPDATE users SET nama_lengkap='$nama' WHERE id=$user_id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['user_name'] = $nama;
        $message = "Profil berhasil diupdate!";
    }
}

if (isset($_POST['change_password'])) {
    $user_id = $_SESSION['user_id'];
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
    
    if (!password_verify($current, $user['password'])) {
        $message = "Password lama salah!";
    } elseif ($new !== $confirm) {
        $message = "Password baru tidak cocok!";
    } elseif (strlen($new) < 6) {
        $message = "Password minimal 6 karakter!";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id=$user_id");
        $message = "Password berhasil diubah!";
    }
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=" . $_SESSION['user_id']));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Pengguna</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial;
            background: #f0f4f8;
        }

        .header {
            background: linear-gradient(#185a9d 100%);
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .header h1 { font-size: 24px; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }

        .logout-btn {
            padding: 8px 16px;
            background: #f44336;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }
        .logout-btn:hover { background: #d32f2f; }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .nav-tabs {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .nav-tabs a {
            padding: 10px 18px;
            margin-right: 10px;
            text-decoration: none;
            background: #e9e9e9;
            color: #333;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.2s;
        }
        .nav-tabs a.active {
            background: #185a9d;
            color: white;
        }
        .nav-tabs a:hover { background: #dcdcdc; }

        .content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        h2, h3 { color: #185a9d; margin-bottom: 15px; }

        .message {
            padding: 15px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .info-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #185a9d;
        }
        .info-box p {
            margin: 10px 0;
            font-size: 15px;
            color: #0d47a1;
        }

        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #444;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        hr { margin: 30px 0; border: none; border-top: 1px solid #ddd; }

        .btn {
            padding: 12px 24px;
            background: #185a9d;
            color: white;
            border-radius: 6px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn:hover { background: #0f4c81; }

    </style>
</head>
<body>

    <div class="header">
        <h1>Dashboard Event Organizer</h1>
        <a href="../autentikasi/logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">

        <div class="nav-tabs">
            <a href="../config/index.php">Data Kegiatan</a>
            <a href="../autentikasi/profile.php" class="active">Profil & Password</a>
        </div>

        <div class="content">

            <?php if ($message): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>

            <h2>Informasi Akun</h2>

            <div class="info-box">
                <p><strong>Email:</strong> <?= $user['email'] ?></p>

                <p><strong>Role:</strong>
                    <?= isset($user['role']) && $user['role'] ? $user['role'] : 'User' ?>
                </p>

                <p><strong>Status:</strong>
                    <span style="color: <?= $user['status_akun'] == 1 ? '#0d8a36' : '#d32f2f' ?>;">
                        <?= $user['status_akun'] == 1 ? 'Aktif' : 'Belum Aktif' ?>
                    </span>
                </p>
            </div>

            <h3>Update Profil</h3>

            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                </div>

                <button type="submit" name="update" class="btn">Update Profil</button>
            </form>

            <hr>

            <h3>Ubah Password</h3>

            <form method="POST">
                <div class="form-group">
                    <label>Password Lama:</label>
                    <input type="password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label>Password Baru:</label>
                    <input type="password" name="new_password" minlength="6" required>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru:</label>
                    <input type="password" name="confirm_password" minlength="6" required>
                </div>

                <button type="submit" name="change_password" class="btn">Ubah Password</button>
            </form>

        </div>
    </div>

</body>
</html>
