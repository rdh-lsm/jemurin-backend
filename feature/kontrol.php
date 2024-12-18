<?php
require_once('../app/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirimkan dari frontend
    $status_jemuran = isset($_POST['status_jemuran']) ? $_POST['status_jemuran'] : '';
    $status_cuaca = isset($_POST['status_cuaca']) ? $_POST['status_cuaca'] : '';
    $suhu_udara = isset($_POST['suhu_udara']) ? $_POST['suhu_udara'] : '';
    $kelembaban = isset($_POST['kelembaban']) ? $_POST['kelembaban'] : '';
    $kontrol = isset($_POST['kontrol']) ? $_POST['kontrol'] : '';

    // Debugging: log data yang diterima
    error_log("Received Data: " . print_r($_POST, true), 3, "../logs/debug.log");

    if ($status_jemuran != '' && $status_cuaca != '' && $suhu_udara != '' && $kelembaban != '' && $kontrol != '') {
        try {
            // Update data di database
            $query = "UPDATE input SET 
                        status_jemuran = :status_jemuran, 
                        status_cuaca = :status_cuaca, 
                        input_suhu = :suhu_udara, 
                        input_kelembaban = :kelembaban, 
                        kontrol = :kontrol 
                      WHERE id = (SELECT MAX(id) FROM input)";
            $stmt = Database::connect()->prepare($query);
            $stmt->bindParam(':status_jemuran', $status_jemuran);
            $stmt->bindParam(':status_cuaca', $status_cuaca);
            $stmt->bindParam(':suhu_udara', $suhu_udara);
            $stmt->bindParam(':kelembaban', $kelembaban);
            $stmt->bindParam(':kontrol', $kontrol);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Pengaturan berhasil diperbarui"]);
            } else {
                echo json_encode(["error" => true, "message" => "Gagal memperbarui pengaturan"]);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, "../logs/database_error.log");
            echo json_encode(["error" => true, "message" => "Terjadi kesalahan database"]);
        }
    } else {
        echo json_encode(["error" => true, "message" => "Data tidak valid"]);
    }
} else {
    echo json_encode(["error" => true, "message" => "Metode request tidak valid"]);
}
?>
