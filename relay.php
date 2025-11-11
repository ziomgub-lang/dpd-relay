<?php
// 🔁 DPD Cloud Relay Proxy (Alpha2)
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// baza DPD Cloud (produkcyjna)
$apiBase = 'https://cloud.dpd.com';

// ścieżka z relay
$path = $_SERVER['REQUEST_URI'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// usuń prefix /api
$forwardPath = preg_replace('#^/api#', '', $path);
$url = $apiBase . $forwardPath;

// cURL config
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

// body w POST/PUT
if ($method !== 'GET') {
    $body = file_get_contents('php://input');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// output
echo json_encode([
    'relay' => 'dpd-relay.onrender.com',
    'forwarded_to' => $url,
    'http_code' => $httpCode,
    'success' => $error === '',
    'error' => $error,
    'response' => json_decode($response, true) ?: $response,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
