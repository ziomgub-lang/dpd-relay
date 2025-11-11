<?php
header('Content-Type: application/json');

// Odczyt danych z POST
$url = $_POST['url'] ?? 'https://cloud.dpd.com/api/v1/setOrder';
$postData = $_POST['data'] ?? '{}';
$partnerName = $_POST['partner_name'] ?? '';
$partnerToken = $_POST['partner_token'] ?? '';
$userId = $_POST['user_id'] ?? '';
$userToken = $_POST['user_token'] ?? '';

// Przygotowanie nagłówków
$headers = [
    'Content-Type: application/json',
];

// Inicjalizacja cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// Wykonanie
$response = curl_exec($ch);
$info = curl_getinfo($ch);
$error = curl_error($ch);
curl_close($ch);

// Wynik
echo json_encode([
    'success' => empty($error),
    'url' => $url,
    'http_code' => $info['http_code'] ?? 0,
    'time' => $info['total_time'] ?? 0,
    'error' => $error,
    'response' => $response,
    'curl_info' => $info,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
