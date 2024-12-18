<?php
require_once('../app/database.php');

header('Content-Type: application/json');

try {
    // Debugging: Menampilkan metode request yang diterima
    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        echo json_encode([
            "error" => true,
            "message" => "Metode request tidak valid, diterima: " . $_SERVER["REQUEST_METHOD"]
        ]);
        exit;
    }

    // Ambil data suhu dan kelembaban terbaru dari database
    $db = Database::connect();
    $query = "SELECT input_suhu, input_kelembaban FROM input ORDER BY id DESC LIMIT 1";
    $stmt = $db->query($query);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data ditemukan
    if ($data) {
        $suhu = $data['input_suhu'];
        $kelembaban = $data['input_kelembaban'];

        // Tentukan status jemuran dan cuaca berdasarkan suhu dan kelembaban
        $status_jemuran = "tertutup"; // Default status
        $status_cuaca = "Mendung"; // Default cuaca

        // Logika untuk menentukan status jemuran dan cuaca
        if ($suhu > 28 && $kelembaban < 50) {
            $status_jemuran = "terbuka"; // Status terbuka jika suhu > 28 dan kelembaban < 50
            $status_cuaca = "Terang";   // Cuaca terang
        } elseif ($suhu <= 28 && $kelembaban >= 50) {
            $status_cuaca = "Hujan";    // Cuaca hujan jika suhu rendah dan kelembaban tinggi
        }

        // Update status jemuran dan cuaca di database
        $query_update = "UPDATE input SET status_jemuran = :status_jemuran, status_cuaca = :status_cuaca WHERE id = (SELECT MAX(id) FROM input)";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->bindParam(':status_jemuran', $status_jemuran);
        $stmt_update->bindParam(':status_cuaca', $status_cuaca);

        // Eksekusi query untuk update
        if ($stmt_update->execute()) {
            echo json_encode([
                "success" => true,
                "status_jemuran" => $status_jemuran,
                "status_cuaca" => $status_cuaca,
                "message" => "Status jemuran dan cuaca berhasil diperbarui"
            ]);
        } else {
            echo json_encode([
                "error" => true,
                "message" => "Gagal memperbarui status jemuran dan cuaca"
            ]);
        }

    } else {
        echo json_encode([
            "error" => true,
            "message" => "Data suhu dan kelembaban tidak ditemukan"
        ]);
    }

    // Disconnect database
    Database::disconnect();
} catch (Exception $e) {
    error_log($e->getMessage(), 3, "../logs/database_error.log");
    echo json_encode([
        "error" => true,
        "message" => "Terjadi kesalahan server. Silakan coba lagi nanti"
    ]);
}
?>
