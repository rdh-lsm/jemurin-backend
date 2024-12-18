<?php
// Menghubungkan ke database
require_once '../app/database.php';

// Fungsi untuk mengambil status jemuran dan menambahkan lokasi Jakarta
function get_status() {
    // Mengambil koneksi ke database
    $db = Database::connect();

    // Menentukan lokasi statis
    $location = "Jakarta";  // Lokasi tetap yang diinginkan

    // Query untuk mengambil data terakhir dari tabel input
    $sql = "SELECT status_jemuran, status_cuaca, input_suhu AS suhu_udara, input_kelembaban AS kelembaban, kontrol FROM input ORDER BY id DESC LIMIT 1";

    // Menyiapkan statement
    $stmt = $db->prepare($sql);

    // Mengeksekusi query
    $stmt->execute();

    // Mengambil hasil
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data ditemukan, mengembalikan sebagai JSON
    if ($result) {
        // Menambahkan informasi lokasi ke dalam response JSON
        $result['location'] = $location;

        echo json_encode($result);
    } else {
        // Jika tidak ada data ditemukan, mengembalikan error
        echo json_encode(["error" => "Tidak ada data"]);
    }

    // Menutup koneksi database
    Database::disconnect();
}

// Panggil fungsi untuk mendapatkan status
get_status();
?>
