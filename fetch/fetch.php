<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require_once '../app/database.php';

// Define your OpenWeatherMap API Key and City Name
$api_key = 'b53e4317504a0aec4287f32f9ebd3734';
$city_name = 'Bandung';  // Replace with the desired city

// URL to fetch data from OpenWeatherMap
$weather_url = "https://api.openweathermap.org/data/2.5/weather?q=$city_name&appid=$api_key&units=metric";

// Fetch the weather data
$weather_data = file_get_contents($weather_url);
$weather = json_decode($weather_data, true);

// Check if we got the weather data
if (isset($weather['main']['temp'])) {
    $current_temperature = $weather['main']['temp']; // Temperature in Celsius
} else {
    $current_temperature = null; // Default to null if there's an error
}

try {
    $db = Database::connect();

    // Fetch the latest data from the database
    $stmt = $db->query("SELECT status_jemuran, status_cuaca, input_kelembaban, kontrol FROM input ORDER BY id DESC LIMIT 1");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Return the data, including the city name (Lokasi)
        echo json_encode([
            "status_jemuran" => $data['status_jemuran'],
            "status_cuaca" => $data['status_cuaca'],
            "suhu_udara" => $current_temperature !== null ? $current_temperature : "Data Tidak Tersedia",  // Use the temperature from OpenWeatherMap
            "kelembaban" => (float)$data['input_kelembaban'],
            "kontrol" => $data['kontrol'],
            "lokasi" => $city_name  // Include city name as 'lokasi'
        ]);
    } else {
        echo json_encode([
            "error" => "Data tidak ditemukan."
        ]);
    }

    Database::disconnect();
} catch (Exception $e) {
    error_log($e->getMessage(), 3, '../app/logs/database_error.log');
    echo json_encode([
        "error" => "Terjadi kesalahan server. Silakan coba lagi nanti."
    ]);
}
?>
