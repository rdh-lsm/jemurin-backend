<?php
require_once '../app/database.php';

header('Content-Type: text/plain');

try {
    $db = Database::connect();

    // Jumlah data yang ingin ditambahkan
    $dataCount = 10;

    for ($i = 0; $i < $dataCount; $i++) {
        // Generate data random
        $input_kelembaban = rand(50, 100); // Nilai kelembaban antara 50% - 100%
        $input_suhu = rand(20, 35); // Nilai suhu antara 20°C - 35°C
        $status_cuaca = ($input_kelembaban > 80) ? "Hujan" : (($input_kelembaban > 60) ? "Mendung" : "Terang");
        $status_jemuran = rand(0, 1) ? "Terbuka" : "Tertutup"; // Random "Terbuka" atau "Tertutup"
        $kontrol = rand(0, 1) ? "otomatis" : "manual"; // Random "otomatis" atau "manual"

        // Masukkan ke database
        $stmt = $db->prepare("INSERT INTO input (input_kelembaban, status_cuaca, status_jemuran, input_suhu, kontrol) VALUES (:input_kelembaban, :status_cuaca, :status_jemuran, :input_suhu, :kontrol)");
        $stmt->bindParam(':input_kelembaban', $input_kelembaban);
        $stmt->bindParam(':status_cuaca', $status_cuaca);
        $stmt->bindParam(':status_jemuran', $status_jemuran);
        $stmt->bindParam(':input_suhu', $input_suhu);
        $stmt->bindParam(':kontrol', $kontrol);

        $stmt->execute();
    }

    echo "$dataCount data random berhasil ditambahkan ke tabel input.";
    Database::disconnect();
} catch (Exception $e) {
    error_log($e->getMessage(), 3, '../app/logs/database_error.log');
    echo "Terjadi kesalahan saat menambahkan data random.";
}
