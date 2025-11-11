<?php
// Universal DPD Relay Proxy
header('Content-Type: application/json');

$path = $_SERVER['REQUEST_URI'];
$apiBase = 'https://cloud.dpd.com';

// Usuń początkowy "/api" z URL, żeby dopasować do ścieżki DPD Cloud
$forwardPath = preg_replace('#^/api#', '', $path);
$url = $apiBase . $forwardPath;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CUSTOMREQUEST => $_SERVER['REQUEST_METHOD'],
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
]);

// Jeśli są dane POST – przekazujemy dalej
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $input = file_get_contents('php://input');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo json_encode([
    'success' => empty($error),
    'url' => $url,
    'http_code' => $httpCode,
    'error' => $error,
    'response' => json_decode($response, true) ?: $response,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
