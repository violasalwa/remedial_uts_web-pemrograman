<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: autentikasi/login.php");
    exit();
}

include '../config/conn_db.php';

// buat tambah kegiatan di event nya 

if (isset($_POST['add_event'])) {
    $nama_event = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal_event = mysqli_real_escape_string($conn, $_POST['tanggal_event']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO events (user_id, nama_event, deskripsi, tanggal_event, lokasi)
            VALUES ($user_id, '$nama_event', '$deskripsi', '$tanggal_event', '$lokasi')";
    mysqli_query($conn, $sql);
}

// buat hapus keegiatan di event nya 
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM events WHERE id = $id");
    header("Location: index.php");
    exit();
}

$events = mysqli_query($conn, 
    "SELECT * FROM events 
     WHERE user_id = " . $_SESSION['user_id'] . "
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard EO</title>
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
        .header .user-info { font-size: 14px; opacity: 0.9; }

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

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
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
        .nav-tabs a:hover { background: #dcdcdc; }
        .nav-tabs a.active {
            background: #185a9d;
            color: white;
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: bold; color: #444; margin-bottom: 5px; }
        .form-group input, textarea {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        textarea { resize: vertical; }

        .btn {
            background: #185a9d;
            color: white;
            padding: 12px 22px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
        }
        .btn:hover { background: #0f4c81; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th {
            padding: 12px;
            background: #185a9d;
            color: white;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover { background: #f2f9ff; }

        .delete-btn {
            background: #F22421;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            transition: 0.2s;
        }
        .delete-btn:hover { background: #c71b18; }

    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1>Dashboard Event Organizer</h1>
            <div class="user-info">
                <?= $_SESSION['user_name'] ?> (<?= $_SESSION['user_email'] ?>)
            </div>
        </div>
        <a href="../autentikasi/logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">

        <div class="nav-tabs">
            <a href="index.php" class="active">Data Kegiatan</a>
            <a href="../autentikasi/profile.php">Profil & Password</a>
        </div>

        <div class="content">
            <h2>Tambah Kegiatan Baru</h2>

            <form method="POST">
                <div class="form-grid">

                    <div class="form-group">
                        <label>Nama Event:</label>
                        <input type="text" name="nama_event" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Event:</label>
                        <input type="date" name="tanggal_event" required>
                    </div>

                    <div class="form-group">
                        <label>Lokasi:</label>
                        <input type="text" name="lokasi" required>
                    </div>

                    <div class="form-group" style="grid-column: 1 / 3;">
                        <label>Deskripsi:</label>
                        <textarea name="deskripsi" rows="3"></textarea>
                    </div>

                </div>

                <button type="submit" name="add_event" class="btn">Tambah Event</button>
            </form>

            <h3 style="margin-top: 30px;">Daftar Kegiatan</h3>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Event</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($events)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_event']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_event']) ?></td>
                        <td><?= htmlspecialchars($row['lokasi']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></td>

                        <td>
                            <a href="?delete=<?= $row['id'] ?>"
                            class="delete-btn"
                            onclick="return confirm('Yakin ingin menghapus event ini?')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>


        </div>
    </div>

</body>
</html>
