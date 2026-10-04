<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";
$error = "";

try {
    $stmtAlat = $pdo->query("SELECT id, nama_alat, jumlah FROM alat_lab");
    $daftarAlat = $stmtAlat->fetchAll();
} catch (PDOException $e) {
    $daftarAlat = [];
    $error = "Gagal mengambil data alat: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_alat = $_POST['id_alat'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';

    if (empty($id_alat) || $jumlah === '') {
        $error = "Semua kolom wajib diisi!";
    } elseif (!is_numeric($jumlah) || $jumlah < 0) {
        $error = "Jumlah stok harus berupa angka dan tidak boleh negatif!";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE alat_lab SET jumlah = :jumlah WHERE id = :id"
            );

            $sukses = $stmt->execute([
                'jumlah' => $jumlah,
                'id' => $id_alat
            ]);

            if ($sukses) {
                $pesan = "Stok alat berhasil diperbarui!";

                $stmtAlat = $pdo->query(
                    "SELECT id, nama_alat, jumlah FROM alat_lab"
                );
                $daftarAlat = $stmtAlat->fetchAll();
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Stok Alat Lab</title>
</head>
<body>

    <h2>Update Stok Alat Laboratorium</h2>

    <p><a href="dashboard.php">&laquo; Kembali ke Dashboard</a></p>

    <?php if ($pesan): ?>
        <p style="color: green;"><?= htmlspecialchars($pesan) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Pilih Alat:</label><br>

        <select name="id_alat" required>
            <option value="">-- Pilih Alat --</option>

            <?php foreach ($daftarAlat as $alat): ?>
                <option value="<?= $alat['id'] ?>">
                    <?= htmlspecialchars($alat['nama_alat']) ?>
                    (Stock Sekarang: <?= $alat['jumlah'] ?>)
                </option>
            <?php endforeach; ?>

        </select><br><br>

        <label>Jumlah Stock Baru:</label><br>
        <input type="number" name="jumlah" min="0" required><br><br>

        <button type="submit">Update Stock</button>
    </form>

</body>
</html>