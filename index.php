<?php
// DPD Relay Proxy + Test page
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Baza DPD Cloud (oficjalna produkcyjna)
$apiBase = 'https://cloud.dpd.com';

// Ścieżka z relay (usuwamy /api prefix)
$path = $_SERVER['REQUEST_URI'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$forwardPath = preg_replace('#^/api#', '', $path);
$url = $apiBase . $forwardPath;

// Jeśli użytkownik wchodzi bez API — pokaz prosty test JSON
if (!str_contains($path, '/api/')) {
    echo json_encode([
        'relay' => 'dpd-relay.onrender.com',
        'status' => '✅ działa poprawnie',
        'usage' => 'Użyj /api/v1/... aby przekierować zapytanie do DPD Cloud',
        'example' => 'https://dpd-relay.onrender.com/api/v1/parcelshopfinder/postcode?country=DE&zipcode=10115'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Konfiguracja cURL
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
]);

if ($method !== 'GET') {
    $body = file_get_contents('php://input');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo json_encode([
    'relay' => 'dpd-relay.onrender.com',
    'forwarded_to' => $url,
    'http_code' => $httpCode,
    'success' => $error === '',
    'error' => $error,
    'response' => json_decode($response, true) ?: $response,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
