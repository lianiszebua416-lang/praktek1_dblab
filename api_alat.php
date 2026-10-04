<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, nama_alat, jumlah FROM alat_lab");
    $data = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data alat"
    ]);
}
?>